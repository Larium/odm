<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Doctrine\Common\Collections\Expr\Expression;
use RuntimeException;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;
use Doctrine\Common\Collections\Expr\Value;
use Google\Cloud\Firestore\CollectionReference;
use function array_key_exists;

class FirestoreExpressionVisitor extends ExpressionVisitor
{
    private $collection;

    private $query;

    /**
     * @var string
     */
    private $queryProxyClass;

    private const OPERATORS = [
        Comparison::EQ => '==',
        Comparison::LT => '<',
        Comparison::LTE => '<=',
        Comparison::GT => '>',
        Comparison::GTE => '>=',
        Comparison::CONTAINS => 'array-contains',
    ];

    public function __construct(CollectionReference $collection, string $queryProxyClass = null)
    {
        $this->queryProxyClass = $queryProxyClass ?: FirestoreQuery::class;
        $this->collection = $collection;
    }

    public function walkComparison(Comparison $comparison)
    {
        $field = $comparison->getField();
        $value = $comparison->getValue()->getValue();
        $operator = $comparison->getOperator();

        if (!array_key_exists($operator, self::OPERATORS)) {
            throw new RuntimeException(
                sprintf('Operator `%s` is not supported', $operator)
            );
        }

        if (null === $this->query) {
            return $this->query = $this->collection->where($field, $operator, $value);
        }

        return $this->query = $this->query->where($field, $operator, $value);
    }

    public function walkValue(Value $value)
    {
        return $value->getValue();
    }

    public function walkCompositeExpression(CompositeExpression $expr)
    {
        $list = $expr->getExpressionList();
        foreach ($list as $child) {
            parent::dispatch($child);
        }

        return $this->query;
    }

    public function dispatch(Expression $expr)
    {
        /** var Query $query */
        $this->query = parent::dispatch($expr);

        $reflection = new \ReflectionClass($this->queryProxyClass);

        return $reflection->newInstanceArgs([$this->query]);
    }
}
