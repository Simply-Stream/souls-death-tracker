<?php

namespace SimplyStream\SoulsDeathBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Counter::class);
    }

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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByAliasInTracker(string $alias, Tracker $tracker): ?Counter
    {
        $qb = $this->getCauses($tracker)
            ->andWhere('c.alias = :alias')
            ->setParameter(':alias', $alias);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllByTracker(Tracker $tracker): array
    {
        return $this->getCauses($tracker)->getQuery()->getResult();
    }

    public function sumTotalByTracker(Tracker $tracker): int
    {
        $qb = $this->getCauses($tracker)
            ->select('SUM(c.deaths)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
