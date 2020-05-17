<?php

namespace App\Controller;

use App\Entity\User;
use App\Enums\UserType;
use App\Entity\Company;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Setup controller.
 */
class SetupController extends BaseController
{
    /**
     * @var ClientManagerInterface
     */
    private $cm;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SetupController constructor.
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param ClientManagerInterface $cm
     * @param EntityManagerInterface $em
     */
    public function __construct(TokenStorageInterface $tokenStorage, ClientManagerInterface $cm, EntityManagerInterface $em)
    {
        parent::__construct($tokenStorage);

        $this->cm = $cm;
        $this->em = $em;
    }

    /**
     * Setup example data
     * @Get("/setup")
     *
     * @return Response
     * @noinspection PhpUnused
     */
    public function setupAction()
    {
        $client    = $this->createClient();
        $admin     = $this->createAdmin();
        $company   = $this->createCompany();
        $product   = $this->createProduct($company);
        $customers = $this->createCustomers($company);

        $data = [
            'client'   => $client,
            'entities' => compact('admin', 'company', 'customers', 'product'),
        ];

        return $this->apiSuccess($data, ['list']);
    }

    /**
     * @return ClientInterface
     */
    private function createClient()
    {
        $client = $this->cm->createClient();
        $client->setRedirectUris(['http://sf4-basic-ecommerce-api.test']);
        $client->setAllowedGrantTypes(['password']);
        $this->cm->updateClient($client);

        return $client;
    }

    /**
     * @return User
     */
    private function createAdmin()
    {
        $user = new User();
        $user->setFirstName('Admin');
        $user->setLastName('User');
        $user->setEmail('admin@sf4-basic-ecommerce-api.test');
        $user->setUsername('admin');
        $user->setEnabled(true);
        $user->setPlainPassword('123456');
        $user->setType(UserType::ADMIN);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @return Company
     */
    private function createCompany()
    {
        $user = new User();
        $user->setFirstName('Company Admin');
        $user->setLastName('User');
        $user->setEmail('company-admin@sf4-basic-ecommerce-api.test');
        $user->setUsername('company_admin');
        $user->setEnabled(true);
        $user->setPlainPassword('123456');
        $user->setType(UserType::COMPANY_ADMIN);
        $this->em->persist($user);
        $this->em->flush();

        $company = new Company();
        $company->setName('ABC Company');
        $company->setOwner($user);
        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    /**
     * @param Company $company
     *
     * @return array
     */
    private function createCustomers(Company $company)
    {
        $users = [];

        for ($i = 1; $i < 4; $i++) {
            $user = new User();
            $user->setFirstName($i . ' - Customer');
            $user->setLastName('User');
            $user->setEmail('customer' . $i . '@sf4-basic-ecommerce-api.test');
            $user->setUsername('customer' . $i);
            $user->setEnabled(true);
            $user->setPlainPassword('123456');
            $user->setType(UserType::CUSTOMER);

            $company->addCustomer($user);

            $this->em->persist($user);
            $users[] = $user;
        }
        $this->em->flush();

        return $users;
    }

    /**
     * @param Company $company
     *
     * @return Product
     */
    private function createProduct(Company $company)
    {
        $product = new Product();
        $product->setName("Example Product");
        $product->setDescription("Example Product's wonderful description");
        $product->setStock(10000);
        $product->setCompany($company);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }
}
