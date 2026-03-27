<?php

declare(strict_types=1);

namespace Larium\ODM\Firestore;

use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use PHPUnit\Framework\TestCase;

class FirestoreQueryTest extends TestCase
{
    public function testGetDocumentUsesDocumentReferenceForId(): void
    {
        $expectedId = 'doc-123';

        $snapshot = $this->createMock(DocumentSnapshot::class);

        $docRef = $this->createMock(DocumentReference::class);
        $docRef->expects($this->once())
            ->method('snapshot')
            ->willReturn($snapshot);

        $collection = $this->createMock(CollectionReference::class);
        $collection->expects($this->once())
            ->method('document')
            ->with($expectedId)
            ->willReturn($docRef);

        $q = new FirestoreQuery($collection);

        $this->assertSame($snapshot, $q->getDocument($expectedId));
    }

    public function testGetDocumentOnFilteredQueryThrows(): void
    {
        $query = $this->createMock(\Google\Cloud\Firestore\Query::class);
        $q = new FirestoreQuery($query);

        $this->expectException(\RuntimeException::class);
        $q->getDocument('any');
    }
}
