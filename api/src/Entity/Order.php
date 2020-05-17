<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom={"id", "orderCode", "quantity", "address", "shippingData", "product", "customer", "company"})
 * @ORM\Entity(repositoryClass="App\Repositories\OrderRepository")
 */
class Order extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(nullable=false, name="company_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("company")
     * @Assert\Type(Company::class)
     * @Assert\NotBlank()
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, name="customer_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("customer")
     * @Assert\Type(User::class)
     * @Assert\NotBlank()
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("product")
     * @Assert\Type(Product::class)
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\Column(type="string", name="order_code")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("order_code")
     * @Assert\Type("string")
     */
    private $orderCode;

    /**
     * @ORM\Column(type="integer", name="quantity")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("quantity")
     * @Assert\Type("int")
     */
    private $quantity;

    /**
     * @ORM\Column(type="text", name="address")
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("address")
     * @Assert\Type("string")
     */
    private $address;

    /**
     * @ORM\Column(type="datetime", name="shipping_date", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\SerializedName("shipping_date")
     * @Assert\DateTime()
     */
    private $shippingDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return Order
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return User
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param User $customer
     *
     * @return Order
     */
    public function setCustomer(User $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderCode()
    {
        return $this->orderCode;
    }

    /**
     * @param string $orderCode
     *
     * @return Order
     */
    public function setOrderCode($orderCode)
    {
        $this->orderCode = $orderCode;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return Order
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return Order
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Order
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getShippingDate()
    {
        return $this->shippingDate;
    }

    /**
     * @param DateTime $shippingDate
     *
     * @return Order
     */
    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    /**
     * Returns order is shipped
     *
     * @return bool
     */
    public function isShipped()
    {
        return !!$this->shippingDate;
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
