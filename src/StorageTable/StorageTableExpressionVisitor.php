<?php

declare(strict_types=1);

namespace Larium\ODM\StorageTable;

use RuntimeException;
use Larium\ODM\QueryProxy;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\Expression;
use MicrosoftAzure\Storage\Table\TableRestProxy;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use MicrosoftAzure\Storage\Table\Models\Filters\BinaryFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\ConstantFilter;
use MicrosoftAzure\Storage\Table\Models\Filters\Filter;
use MicrosoftAzure\Storage\Table\Models\Filters\PropertyNameFilter;
use MicrosoftAzure\Storage\Table\Models\QueryEntitiesOptions;
use MicrosoftAzure\Storage\Table\Models\Filters\QueryStringFilter;

class StorageTableExpressionVisitor extends ExpressionVisitor
{
    private const COMPARISON_OPERATORS = [
        Comparison::EQ => 'eq',
        Comparison::LT => 'lt',
        Comparison::LTE => 'le',
        Comparison::GT => 'gt',
        Comparison::GTE => 'ge',
        Comparison::NEQ => 'ne',
    ];

    private const BOOLEAN_OPERATORS = [
        CompositeExpression::TYPE_AND => 'and',
        CompositeExpression::TYPE_OR => 'or',
    ];

    private QueryEntitiesOptions $options;

    private TableRestProxy $proxy;

    private string $table;

    public function __construct(TableRestProxy $proxy, string $table)
    {
        $this->proxy = $proxy;
        $this->table = $table;
        $this->options = new QueryEntitiesOptions();
    }

    public function walkComparison(Comparison $comparison)
    {
        $operator = $comparison->getOperator();
        if (!array_key_exists($operator, self::COMPARISON_OPERATORS)) {
            throw new RuntimeException(
                sprintf('Operator `%s` is not supported', $operator)
            );
        }
        $op = self::COMPARISON_OPERATORS[$operator];
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue();

        $propertyFilter = new PropertyNameFilter($field);
        $valueFilter = new ConstantFilter(null, $value);

        return new BinaryFilter($propertyFilter, $op, $valueFilter);
    }

    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    public function walkCompositeExpression(CompositeExpression $expr)
    {
        $operator = $expr->getType();
        list($left, $right) = $expr->getExpressionList();

        $leftFilter = parent::dispatch($left);
        $rightFilter = parent::dispatch($right);

        return new BinaryFilter($leftFilter, self::BOOLEAN_OPERATORS[$operator], $rightFilter);
    }

    public function dispatch(Expression $expr): QueryProxy
    {
        /** @var Filter $filter */
        $filter = parent::dispatch($expr);

        $this->options->setFilter($filter);

        return new StorageTableQuery($this->proxy, $this->table, $this->options);
    }
}
