<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;

class Collection
{
    private $name;

    private $expressionVisitor;

    private $documentVisitor;

    public function __construct(
        string $name,
        ExpressionVisitor $visitor,
        DocumentVisitor $documentVisitor
    ) {
        $this->name = $name;
        $this->expressionVisitor = $visitor;
        $this->documentVisitor = $documentVisitor;
    }

    /**
     * @return array Document[]
     */
    public function getDocuments(Criteria $criteria): array
    {
        $expr = $criteria->getWhereExpression();

        $queryProxy = $this->expressionVisitor->dispatch($expr);

        $it = $queryProxy->getIterator();
        $data = [];
        foreach ($it as $item) {
            $data[] = $this->documentVisitor->visit($item);
        }

        return $data;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
