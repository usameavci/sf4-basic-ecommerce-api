<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;

/**
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("client_id")
     * @Serializer\Accessor(getter="getPublicId")
     */
    protected $randomId;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("client_secret")
     */
    protected $secret;

    public function __construct()
    {
        parent::__construct();
    }
}
