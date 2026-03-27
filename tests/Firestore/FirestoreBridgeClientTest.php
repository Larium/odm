<?php

declare(strict_types = 1);

namespace Larium\ODM\Firestore;

use Larium\ODM\Configuration;
use Larium\ODM\Document\UserGateway;
use Larium\ODM\DocumentManager;
use PHPUnit\Framework\TestCase;

class FirestoreBridgeClientTest extends TestCase
{
    private Configuration $config;

    private DocumentManager $dm;

    public function setUp(): void
    {
        $this->config = new Configuration();
        $this->config->setDocumentsPaths([
            __DIR__ . '/../Document'
        ]);
        $this->config->setClientFactory(new FirestoreClientFactory());
        $this->dm = new DocumentManager($this->config);
    }

    public function testGetCollection(): void
    {
        $userGateway = new UserGateway(
            provider: 'mailgun',
            credentials: ['apiKey' => '123456789'],
            user: '1',
        );

        $this->dm->persist($userGateway);
        $this->dm->flush();
/*
        $repo = $this->dm->getRepository(UserGateway::class);
        $userGateway = $repo->getDocument('670af32cbe9bc');

        print_r($userGateway);*/
    }
}
