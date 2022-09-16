<?php

namespace SimplyStream\SoulsDeathBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use SimplyStream\SoulsDeathBundle\Entity\Tracker;
use SimplyStream\SoulsDeathBundle\Entity\UserInterface;
use SimplyStream\SoulsDeathBundle\Repository\CounterRepository;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;

class TrackerService
{
    /** @var TrackerRepository */
    protected TrackerRepository $trackerRepository;

    /** @var CounterRepository */
    protected CounterRepository $counterRepository;

    /** @var EntityManagerInterface */
    protected EntityManagerInterface $entityManager;

    /** @var TokenGenerator */
    protected TokenGenerator $tokenGenerator;

    /**
     * @param TrackerRepository      $trackerRepository
     * @param CounterRepository      $counterRepository
     * @param EntityManagerInterface $entityManager
     * @param TokenGenerator         $tokenGenerator
     */
    public function __construct(
        TrackerRepository $trackerRepository,
        CounterRepository $counterRepository,
        EntityManagerInterface $entityManager,
        TokenGenerator $tokenGenerator
    ) {
        $this->trackerRepository = $trackerRepository;
        $this->counterRepository = $counterRepository;
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param string $id
     *
     * @return Tracker|null
     */
    public function get(string $id): ?Tracker
    {
        return $this->trackerRepository->find($id);
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     */
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotal(Tracker $tracker): int
    {
        try {
            return $this->counterRepository->sumTotalByTracker($tracker);
        } catch (\TypeError $exception) {
            // @TODO: When there's no counters, this exception will be thrown. Check for improvements
            return 0;
        }
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

    /**
     * @return string
     * @throws \Exception
     */
    public function generatePublicToken(): string
    {
        return $this->tokenGenerator->generate();
    }
}
