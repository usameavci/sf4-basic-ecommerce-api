<?php

namespace App\Controller;

use Exception;
use App\Entity\Product;
use App\Enums\UserType;
use App\Form\ProductType;
use App\Repositories\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Product controller.
 * @Route("/api", name="api_")
 */
class ProductController extends BaseController
{
    /**
     * List products.
     * @Get("/products")
     *
     * @return Response
     * @throws Exception
     */
    public function indexAction()
    {
        if (!$this->getUser()->isCompanyAdmin()) {
            return $this->apiError('Forbidden action!');
        }

        /** @var ProductRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Product::class);

        $products = $repository->getProductsByCompany($this->getCompany());

        return $this->apiSuccess($products, ['list']);
    }

    /**
     * Create product.
     * @Post("/products")
     *
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function storeAction(ValidatorInterface $validator)
    {
        if (!$this->getUser()->isCompanyAdmin()) {
            return $this->apiError('Forbidden action!');
        }

        if ($this->getUser()->getType() !== UserType::COMPANY_ADMIN) {
            return $this->apiError('Products can only created by company admins!');
        }

        $product = new Product();
        $product->fill($this->getRequestBody());
        $product->setCompany($this->getCompany());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($product);
        if ($errors->count() > 0) {
            return $this->apiValidationError($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->apiSuccess($product, ['detail']);
    }

    /**
     * Show company.
     * @Get("/products/{productId}")
     *
     * @param int $productId
     *
     * @return Response
     */
    public function showAction(int $productId)
    {
        if (!$this->getUser()->isCompanyAdmin()) {
            return $this->apiError('Forbidden action!');
        }

        /** @var ProductRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $product    = $repository->findByIdFromCompany($this->getCompany(), $productId);

        if (!$product) {
            return $this->apiError('Product not found!');
        }

        if (!$product->isOwnedCompany($this->getUser()->getCompany())) {
            return $this->apiError('Product can only be seen by the company admin!');
        }

        return $this->apiSuccess($product);

    }
}
