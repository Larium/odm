<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use ArrayIterator;
use Doctrine\Common\Collections\Criteria;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Query;
use Larium\ODM\QueryProxyStub;
use PHPUnit\Framework\TestCase;

class FirestoreExpressionVisitorTest extends TestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = new FirestoreClient();
    }

    public function testExpressionVisitor()
    {
        $c = Criteria::create()
            ->where(Criteria::expr()->eq('first', 'Andreas'));

        $expr = $c->getWhereExpression();

        $exprVisitor = new FirestoreExpressionVisitor(
            $this->client->collection('users'),
            QueryProxyStub::class
        );

        $query = $exprVisitor->dispatch($expr);
        $q = $query->getInnerQuery();

        $this->assertTrue($q->queryHas('where'));

        $key = $q->queryKey('where');

        $this->assertEquals(Query::OP_EQUAL, $key['compositeFilter']['filters'][0]['fieldFilter']['op']);
    }
}
