<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Larium\ODM\Persister;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\ExpressionVisitor;

class Collection
{
    private $name;

    private $expressionVisitor;

    private $documentVisitor;

    private $persister;

    private $queryProxy;

    public function __construct(
        string $name,
        ExpressionVisitor $visitor,
        DocumentVisitor $documentVisitor,
        Persister $persister,
        QueryProxy $queryProxy
    ) {
        $this->name = $name;
        $this->expressionVisitor = $visitor;
        $this->documentVisitor = $documentVisitor;
        $this->persister = $persister;
        $this->queryProxy = $queryProxy;
    }

    public function getDocument(string $id): Document
    {
        return $this->documentVisitor
                    ->visit($this->queryProxy->getDocument($id));
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

    public function persist(Document $document): void
    {
        $this->persister->persist($document);
    }

    public function update(Document $document): void
    {
        $this->persister->update($document);
    }

    public function remove(Document $document): void
    {
        $this->persister->remove($document);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
