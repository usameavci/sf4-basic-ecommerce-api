<?php

namespace App\Repositories;

use Exception;
use Doctrine\ORM\Query;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class BaseRepository extends EntityRepository
{
    /**
     * @var int
     */
    protected $perPage;

    /**
     * @param int $perPage
     *
     * @return $this
     * @noinspection PhpUnused
     */
    public function setPaginationConfig(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function paginate(Query $query)
    {
        $request     = Request::createFromGlobals();
        $currentPage = (int) $request->query->get('page', 1);
        $perPage     = $this->perPage ?? (int) $request->query->get('per_page', 20);

        $query->setFirstResult(($currentPage - 1) * $perPage);
        $query->setMaxResults($perPage);

        $paginator  = new Paginator($query);
        $totalItems = $paginator->count();

        return [
            'data' => $query->getResult(),
            'meta' => [
                'current_page' => $currentPage,
                'last_page'    => (int) ceil($totalItems / $perPage),
                'per_page'     => $perPage,
                'total_items'  => $totalItems,
            ],
        ];
    }

    /**
     * @param int                                        $id
     * @param \Doctrine\Common\Collections\Criteria|null $criteria
     *
     * @return Entity|null
     */
    public function findById(int $id, ?Criteria $criteria = null)
    {
        try {
            $qb = $this->createQueryBuilder('QB');

            $qb->andWhere('QB.id = :id')->setParameter('id', $id);

            if ($criteria) {
                $qb->addCriteria($criteria);
            }

            $query = $qb->getQuery();

            return $query->getSingleResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('QB');

        $query = $qb->getQuery();

        return $this->paginate($query);
    }
}
