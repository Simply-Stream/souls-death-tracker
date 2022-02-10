<?php

namespace SimplyStream\SoulsDeathBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use SimplyStream\SoulsDeathBundle\Event\CommandExecutionEvent;
use SimplyStream\SoulsDeathBundle\Event\CommandExecutionSuccessEvent;
use SimplyStream\SoulsDeathBundle\Repository\CounterRepository;
use SimplyStream\SoulsDeathBundle\Repository\TrackerRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TrackerCommandEventSubscriber implements EventSubscriberInterface
{
    protected ServiceEntityRepositoryInterface $userRepository;
    protected TrackerRepository $trackerRepository;
    protected CounterRepository $counterRepository;
    protected EntityManagerInterface $entityManager;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ServiceEntityRepositoryInterface $userRepository,
        TrackerRepository $trackerRepository,
        CounterRepository $counterRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->trackerRepository = $trackerRepository;
        $this->counterRepository = $counterRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JsonException
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
            $counter = $this->counterRepository->findOneByAliasInTracker($command[1], $tracker);

            if ($counter) {
                if (isset($command[2]) && $command[2] === 'killed') {
                    $counter->setSuccessful(true);
                } elseif (isset($command[2])) {
                    $operator = $command[2][0];

                    if (in_array($operator, ['+', '-', '='])) {
                        $value = (int)substr($command[2], 1);
                    } else {
                        $value = $command[2];
                    }

                    $newDeaths = match ($operator) {
                        '=' => $value,
                        '-' => $counter->getDeaths() - $value,
                        default => $counter->getDeaths() + $value,
                    };

                    $counter->setDeaths($newDeaths);
                } else {
                    $counter->setDeaths($counter->getDeaths() + 1);
                }

                $this->entityManager->persist($counter);
                $this->entityManager->flush();

                $event = new CommandExecutionSuccessEvent($counter, $user, $channel);
                $this->eventDispatcher->dispatch($event);
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
