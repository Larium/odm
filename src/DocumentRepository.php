<?php

declare(strict_types = 1);

namespace Larium\ODM;

use Doctrine\Common\Collections\Criteria;
use Larium\ODM\Mapping\Metadata\DataMap;

class DocumentRepository
{
    /**
     * @var DocumentManager
     */
    private $dm;
    /**
     * @var DataMap
     */
    private $dataMap;

    /**
     * @var Hydrator
     */
    private $hydrator;

    public function __construct(DocumentManager $dm, DataMap $dataMap)
    {
        $this->dm = $dm;
        $this->dataMap = $dataMap;
        $this->hydrator = new Hydrator($dataMap);
    }

    public function getDocumentManager(): DocumentManager
    {
        return $this->dm;
    }

    public function getDocument(string $id): object
    {
        $doc = $this->dm->getClient()
            ->getCollection($this->dataMap->getCollectionName())
            ->getDocument($id);

        return $this->hydrate($doc);
    }

    public function getDocuments(Criteria $c): array
    {
        $docs = $this->dm->getClient()
            ->getCollection($this->dataMap->getCollectionName())
            ->getDocuments($c);

        return $this->hydrateCollection($docs);
    }

    private function hydrate(Document $doc): object
    {
        $en = $this->hydrator->hydrate($doc);

        $this->dm->getUnitOfWork()->registerClean($doc->getId(), $en);
        $this->dm->getUnitOfWork()->registerOriginal($doc, $en);

        return $en;
    }

    private function hydrateCollection(array $docs): array
    {
        return array_map(function (Document $doc) {
            return $this->hydrate($doc);
        }, $docs);
    }
}
