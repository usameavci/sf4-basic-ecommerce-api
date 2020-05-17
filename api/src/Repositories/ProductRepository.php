<?php

namespace App\Repositories;

use Exception;
use App\Entity\Company;
use App\Entity\Product;
use Doctrine\Common\Collections\Criteria;

class ProductRepository extends BaseRepository
{
    /**
     * @param Company $company
     *
     * @return array
     * @throws Exception
     */
    public function getProductsByCompany(Company $company)
    {
        $qb = $this->createQueryBuilder('P');

        $query = $qb
            ->where('P.company = :company')
            ->setParameter('company', $company)
            ->getQuery();

        return $this->paginate($query);
    }

    /**
     * @param Company $company
     * @param int     $id
     *
     * @return Product
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public function findByIdFromCompany(Company $company, int $id)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('company', $company));

        return $this->findById($id, $criteria);
    }
}
