<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Company controller.
 * @Route("/api", name="api_")
 */
class UserController extends BaseController
{
    /**
     * Current user detail.
     * @Get("/me")
     *
     * @return Response
     */
    public function meAction()
    {
        return $this->apiSuccess($this->getUser(), ['detail']);
    }

}
