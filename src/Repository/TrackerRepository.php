<?php

namespace SimplyStream\SoulsDeathBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;

/**
 * @method Tracker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracker[]    findAll()
 * @method Tracker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Tracker[]    findByOwner(UserInterface $user, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracker::class);
    }
}
