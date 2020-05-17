<?php

namespace App\Repositories;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Company;
use Doctrine\Common\Collections\Criteria;

class OrderRepository extends BaseRepository
{
    /**
     * @param Company $company
     *
     * @return array
     */
    public function getOrdersByCompany(Company $company)
    {
        $qb = $this->createQueryBuilder('O');

        $query = $qb
            ->where('O.company = :company')
            ->setParameter('company', $company)
            ->getQuery();

        return $this->paginate($query);
    }

    /**
     * @param User $customer
     *
     * @return array
     */
    public function getOrdersByCustomer(User $customer)
    {
        $qb = $this->createQueryBuilder('O');

        $query = $qb
            ->where('O.customer = :customer')
            ->setParameter('customer', $customer)
            ->getQuery();

        return $this->paginate($query);
    }

    /**
     * @param User $customer
     * @param int  $id
     *
     * @return Order
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function findByIdFromCustomer(User $customer, int $id)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('customer', $customer));

        return $this->findById($id, $criteria);
    }

    /**
     * @param Company $company
     * @param int     $id
     *
     * @return Order
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function findByIdFromCompany(Company $company, int $id)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('company', $company));

        return $this->findById($id, $criteria);
    }
}
