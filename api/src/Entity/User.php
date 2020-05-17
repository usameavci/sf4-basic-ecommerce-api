<?php

namespace App\Entity;

use App\Enums\UserType;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Serializer\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("username")
     */
    protected $username;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("username_canonical")
     */
    protected $usernameCanonical;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("email")
     */
    protected $email;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("email_canonical")
     */
    protected $emailCanonical;

    /**
     * @ORM\Column(type="string", name="first_name")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("first_name")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", name="last_name")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("last_name")
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true, "default": UserType::CUSTOMER })
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("type")
     */
    private $type = UserType::CUSTOMER;

    /**
     * @ORM\OneToOne(targetEntity="Company", mappedBy="owner")
     * @Serializer\Expose()
     * @Serializer\Groups({"detail"})
     * @Serializer\SerializedName("company")
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity="Company", mappedBy="customers")
     * @Serializer\Expose()
     * @Serializer\Groups({"admin"})
     * @Serializer\SerializedName("company")
     */
    private $companies;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     *
     * @return User
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Determine user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getType() === UserType::ADMIN;
    }

    /**
     * Determine user is company admin
     *
     * @return bool
     */
    public function isCompanyAdmin()
    {
        return $this->getType() === UserType::COMPANY_ADMIN;
    }

    /**
     * Determine user is customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->getType() === UserType::CUSTOMER;
    }
}
