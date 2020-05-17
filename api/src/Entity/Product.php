<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Entity(repositoryClass="App\Repositories\ProductRepository")
 */
class Product extends BaseEntity
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
     * @ORM\Column(type="text", name="name")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("name")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", name="description")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("description")
     * @Assert\Type("string")
     */
    private $description;

    /**
     * @ORM\Column(type="integer", name="stock", options={"default": 100})
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("stock")
     * @Assert\Type("int")
     */
    private $stock = 100;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(nullable=false, fieldName="company", name="company_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("company")
     * @Assert\Type(Company::class)
     * @Assert\NotBlank()
     */
    private $company;

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
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return string
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param string $stock
     *
     * @return string
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

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
     * @return Product
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Determine order is owned by provided company
     *
     * @param Company $company
     *
     * @return bool
     */
    public function isOwnedCompany($company)
    {
        return $this->getCompany()->getId() === $company->getId();
    }
}
