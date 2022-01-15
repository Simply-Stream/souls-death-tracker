<?php

namespace App\Repository;

use App\Entity\Counter;
use App\Entity\Tracker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Counter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Counter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Counter[]    findAll()
 * @method Counter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Counter::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByAliasInTracker(string $alias, Tracker $tracker): ?Counter
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->join('c.section', 's')
            ->join('s.tracker', 't')
            ->where('s.tracker = :tracker')
            ->andWhere('c.alias = :alias')
            ->setParameter(':alias', $alias)
            ->setParameter(':tracker', $tracker);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
