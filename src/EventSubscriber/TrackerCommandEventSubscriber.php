<?php

namespace App\EventSubscriber;

use App\Event\CommandExecutionEvent;
use App\Repository\CounterRepository;
use App\Repository\TrackerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackerCommandEventSubscriber implements EventSubscriberInterface
{
    protected UserRepository $userRepository;
    protected TrackerRepository $trackerRepository;
    protected CounterRepository $counterRepository;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        TrackerRepository $trackerRepository,
        CounterRepository $counterRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->trackerRepository = $trackerRepository;
        $this->counterRepository = $counterRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onCommandExecutionEvent(CommandExecutionEvent $event)
    {
        $channel = $event->getChannel();
        $user = $this->userRepository->findOneBy(['username' => $channel]);
        $tracker = $this->trackerRepository->findOneBy(['commandName' => $event->getCommand(), 'owner' => $user]);
        $userstate = $event->getChatMessage()['userState'];

        if ($tracker && $user &&
            (
                $user->getDisplayName() === $userstate['display-name'] ||
                $userstate['mod'] ||
                in_array('vip/1', $userstate['badges'], true)
            )
        ) {
            $command = explode(' ', substr($event->getChatMessage()['trailing'], 2));
            $counter = $this->counterRepository->findByAliasInTracker($command[1], $tracker);

            if ($counter) {
                if (isset($command[2]) && $command[2] === 'killed') {
                    $counter->setSuccessful(true);
                } else {
                    $addBy = 1;
                    if (isset($command[2])) {
                        $addBy = (int)$command[2];
                    }

                    $newDeaths = $counter->getDeaths() + $addBy;

                    $counter->setDeaths($newDeaths);
                }

                $this->entityManager->persist($counter);
                $this->entityManager->flush();
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandExecutionEvent::class => 'onCommandExecutionEvent',
        ];
    }
}
