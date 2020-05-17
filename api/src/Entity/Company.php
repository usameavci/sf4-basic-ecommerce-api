<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="companies")
 * @Serializer\ExclusionPolicy("all")
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("name")
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="company")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("owner")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Order", mappedBy="company")
     * @ORM\JoinColumn(nullable=false, name="id", referencedColumnName="company")
     * @Serializer\Expose()
     * @Serializer\Groups({"owner", "admin"})
     * @Serializer\SerializedName("orders")
     */
    private $orders;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="companies", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="company_customers",
     *     joinColumns={
     *          @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     *     }
     * )
     * @Serializer\Expose()
     * @Serializer\Groups({"owner", "admin"})
     * @Serializer\SerializedName("customers")
     */
    private $customers;

    /**
     * Company constructor.
     */
    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Company
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return Company
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param User $customer
     *
     * @return $this
     */
    public function addCustomer(User $customer) : Company
    {
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * @param User $customer
     *
     * @return bool
     */
    public function removeCustomer(User $customer) : bool
    {
        return $this->customers->removeElement($customer);
    }

    /**
     * @return Collection
     */
    public function getCustomers() : Collection
    {
        return $this->customers;
    }
}
