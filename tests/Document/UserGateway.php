<?php

declare(strict_types=1);

namespace Larium\ODM\Document;

use Larium\ODM\Mapping\Annotation as ODM;

/**
 * @ODM\Document(collection="user_gateways")
 */
class UserGateway
{
    /**
     * @ODM\Id(type="string")
     */
    private string $id;

    /**
     * @ODM\Field(type="string")
     */
    private string $provider;

    /**
     * @ODM\Field(type="array")
     */
    private array $credentials;

    /**
     * @ODM\Field(type="string")
     */
    private ?string $description = null;

    /**
     * @ODM\Field(type="string")
     */
    private string $user;

    public function __construct(
        string $provider,
        array $credentials,
        string $user,
        ?string $description = null
    ) {
        $this->id = uniqid();
        $this->provider = $provider;
        $this->credentials = $credentials;
        $this->user = $user;
        $this->description = $description;
    }
}
