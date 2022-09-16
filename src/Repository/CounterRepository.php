<?php

namespace SimplyStream\SoulsDeathBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;

/**
 * @method Counter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Counter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Counter[]    findAll()
 * @method Counter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CounterRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Counter::class);
    }

    /**
     * @param Tracker $tracker
     *
     * @return QueryBuilder
     */
    protected function getCauses(Tracker $tracker): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->join('c.section', 's')
            ->join('s.tracker', 't')
            ->where('s.tracker = :tracker')
            ->setParameter(':tracker', $tracker);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByAliasInTracker(string $alias, Tracker $tracker): ?Counter
    {
        $qb = $this->getCauses($tracker)
            ->andWhere('c.alias = :alias')
            ->setParameter(':alias', $alias);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Tracker $tracker
     *
     * @return array
     */
    public function findAllByTracker(Tracker $tracker): array
    {
        return $this->getCauses($tracker)->getQuery()->getResult();
    }

    /**
     * @param Tracker $tracker
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function sumTotalByTracker(Tracker $tracker): int
    {
        $qb = $this->getCauses($tracker)
            ->select('SUM(c.deaths)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
