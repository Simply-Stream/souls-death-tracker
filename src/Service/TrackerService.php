<?php

namespace SimplyStream\SoulsDeathBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use SimplyStream\SoulsDeathBundle\Repository\CounterRepository;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;

class TrackerService
{
    protected TrackerRepository $trackerRepository;

    protected CounterRepository $counterRepository;

    protected EntityManagerInterface $entityManager;

    public function __construct(
        TrackerRepository $trackerRepository,
        CounterRepository $counterRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->trackerRepository = $trackerRepository;
        $this->counterRepository = $counterRepository;
        $this->entityManager = $entityManager;
    }

    public function get(string $id): ?Tracker
    {
        return $this->trackerRepository->find($id);
    }

    public function getByOwner(UserInterface $user): array
    {
        return $this->trackerRepository->findByOwner($user);
    }

    /**
     * Returns the total number of deaths for given tracker
     *
     * @param Tracker $tracker
     *
     * @return int
     */
    public function getTotal(Tracker $tracker): int
    {
        return $this->counterRepository->sumTotalByTracker($tracker);
    }

    /**
     * Saves the tracker
     *
     * @param Tracker $tracker
     * @param bool    $flush
     *
     * @return Tracker
     */
    public function save(Tracker $tracker, bool $flush = false): Tracker
    {
        $this->entityManager->persist($tracker);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $tracker;
    }
}
