<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Company;
use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Setup controller.
 */
class BaseController extends FOSRestController
{
    /**
     * @var null|int
     */
    protected $statusCode = null;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return User|null
     *
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    protected function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * @return Company|null
     */
    protected function getCompany()
    {
        if (!$this->getUser()) {
            return null;
        }

        return $this->getUser()->getCompany();
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param array|object $data
     * @param array|null   $groups
     *
     * @return Response
     */
    protected function apiSuccess($data, $groups = null)
    {
        $view = $this->view($data, $this->statusCode ?? Response::HTTP_OK);

        if ($groups) {
            $context = new Context();
            $context->setGroups($groups);
            $view->setContext($context);
        }

        return $this->handleView($view);
    }

    /**
     * @param string $message
     * @param array  $extras
     *
     * @return Response
     */
    protected function apiError(string $message, array $extras = [])
    {
        $view = $this->view(array_merge_recursive([
            'error_message' => $message,
        ], $extras), $this->statusCode ?? Response::HTTP_BAD_REQUEST);

        return $this->handleView($view);
    }

    /**
     * @param ConstraintViolationList $errors
     *
     * @param array                   $extras
     *
     * @return Response
     */
    protected function apiValidationError(ConstraintViolationList $errors, array $extras = [])
    {
        $message = [];
        foreach ($errors as $error) {
            $message[$error->getPropertyPath()] = [
                "property" => $error->getPropertyPath(),
                "value"   => $error->getInvalidValue(),
                "message" => $error->getMessage(),
            ];
        }

        $view = $this->view(array_merge_recursive([
            'validation_errors' => $message,
        ], $extras), $this->statusCode ?? Response::HTTP_BAD_REQUEST);

        return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->handleView($view);
    }

    /**
     * Returns current request body
     *
     * @return array
     */
    protected function getRequestBody()
    {
        $request = Request::createFromGlobals();

        return json_decode($request->getContent(), true);
    }
}
