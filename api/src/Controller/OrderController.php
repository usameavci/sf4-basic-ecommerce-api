<?php

namespace App\Controller;

use DateTime;
use App\Helpers\Str;
use App\Entity\Order;
use App\Enums\UserType;
use App\Entity\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Order controller.
 * @Route("/api", name="api_")
 *
 * @noinspection AnnotationDeprecatedInspection
 */
class OrderController extends BaseController
{
    /** @var ObjectManager */
    protected $em;

    /** @var OrderRepository */
    protected $repository;

    /** @var ProductRepository */
    private $repositoryProduct;

    /**
     * OrderController constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($tokenStorage);

        $this->em                = $em;
        $this->repository        = $em->getRepository(Order::class);
        $this->repositoryProduct = $em->getRepository(Product::class);
    }

    /**
     * List orders.
     * @Rest\Get("/orders")
     *
     * @return Response
     */
    public function indexAction()
    {
        switch ( $this->getUser()->getType() ) {
            case UserType::COMPANY_ADMIN:
                $orders = $this->repository->getOrdersByCompany($this->getCompany());
                break;
            case UserType::CUSTOMER:
                $orders = $this->repository->getOrdersByCustomer($this->getUser());
                break;
            default:
                $orders = $this->repository->findAll();
                break;
        }

        return $this->apiSuccess($orders, ["list"]);
    }

    /**
     * Create order.
     * @Rest\Post("/orders")
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function storeAction(ValidatorInterface $validator)
    {
        $user = $this->getUser();

        if ($user->getType() !== UserType::CUSTOMER) {
            return $this->apiError('Orders can only created by customers!');
        }

        $requestBody = $this->getRequestBody();

        $order = new Order();
        $order->fill($requestBody);

        /** @var Product $product */
        $product = $this->repositoryProduct->findById($requestBody['product']);
        if (!$product) {
            return $this->apiError('Product not found!');
        }
        $order->setCustomer($user);
        $order->setProduct($product);
        $order->setCompany($product->getCompany());
        $order->setOrderCode(Str::random(10));

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($order);
        if ($errors->count() > 0) {
            return $this->apiValidationError($errors);
        }

        $this->em->persist($order);
        $this->em->flush();

        $product = $order->getProduct();
        $product->setStock($product->getStock() - $order->getQuantity());
        $this->em->persist($product);
        $this->em->flush();

        return $this->apiSuccess($order, ['detail']);
    }

    /**
     * Show order.
     * @Rest\Get("/orders/{orderId}")
     *
     * @param int $orderId
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function showAction(int $orderId)
    {
        switch ( $this->getUser()->getType() ) {
            case UserType::COMPANY_ADMIN:
                $order = $this->repository->findByIdFromCompany($this->getCompany(), $orderId);
                break;
            case UserType::CUSTOMER:
                $order = $this->repository->findByIdFromCustomer($this->getUser(), $orderId);
                break;
            default:
                $order = $this->repository->findById($orderId);
                break;
        }

        if (!$order) {
            return $this->apiError('Order not found!');
        }

        return $this->apiSuccess($order, ['detail']);
    }

    /**
     * Ship order.
     * @Rest\Post("/orders/{orderId}/ship-order")
     *
     * @param int $orderId
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function shipOrderAction(int $orderId)
    {
        $user = $this->getUser();
        if (!$user->isCompanyAdmin()) {
            return $this->apiError('Forbidden action!');
        }

        $company = $this->getCompany();

        /** @var Order $order */
        $order = $this->repository->findById($orderId);

        if (!$order) {
            return $this->apiError('Order not found!');
        }

        if (!$order->isOwnedCompany($company)) {
            return $this->apiError('Order can only be updated by the owner!');
        }

        if ($order->isShipped()) {
            return $this->apiError('Order already shipped!');
        }

        $order = $this->repository->findByIdFromCompany($this->getCompany(), $orderId);

        $order->setShippingDate(new DateTime());

        $this->em->persist($order);
        $this->em->flush();

        return $this->apiSuccess($order, ['detail']);
    }

    /**
     * Ship order.
     * @Rest\Put("/orders/{orderId}")
     *
     * @param ValidatorInterface $validator
     * @param int                $orderId
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function updateAction(ValidatorInterface $validator, int $orderId)
    {
        /** @var Order $order */
        $order   = $this->repository->findById($orderId);
        $hasEdit = $order->getCustomer()->getId() === $this->getUser()->getId()
            || $order->getCompany()->getId() === $this->getCompany()->getId();

        if (!$order) {
            return $this->apiError('Order not found!');
        }

        if (!$hasEdit) {
            return $this->apiError('The order can only be edit by the customer or company that owns the order!');
        }

        if ($order->isShipped()) {
            return $this->apiError('Shipped orders can not be edited!');
        }

        $oldQuantity = $order->getQuantity();
        $requestBody = $this->getRequestBody();

        $order->fill($requestBody);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($order);
        if ($errors->count() > 0) {
            return $this->apiValidationError($errors);
        }

        $this->em->persist($order);
        $this->em->flush();

        $product = $order->getProduct();
        $product->setStock($product->getStock() + ($oldQuantity - $order->getQuantity()));
        $this->em->persist($product);
        $this->em->flush();

        return $this->apiSuccess($order, ['detail']);
    }
}
