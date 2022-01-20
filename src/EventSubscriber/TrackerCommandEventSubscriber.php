<?php

namespace SimplyStream\SoulsDeathBundle\EventSubscriber;

use App\Event\CommandExecutionEvent;
use App\Repository\CounterRepository;
use App\Repository\TrackerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class TrackerCommandEventSubscriber implements EventSubscriberInterface
{
    protected UserRepository $userRepository;
    protected TrackerRepository $trackerRepository;
    protected CounterRepository $counterRepository;
    protected EntityManagerInterface $entityManager;
    protected HubInterface $hub;
    protected ProducerInterface $producer;

    public function __construct(
        UserRepository $userRepository,
        TrackerRepository $trackerRepository,
        CounterRepository $counterRepository,
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        ProducerInterface $producer
    ) {
        $this->userRepository = $userRepository;
        $this->trackerRepository = $trackerRepository;
        $this->counterRepository = $counterRepository;
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->producer = $producer;
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
            $updatedCounter = [];

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

                    $updatedCounter += [
                        'id' => $counter->getId(),
                        'cause' => $counter->getCause(),
                        'alias' => $counter->getAlias(),
                        'successful' => $counter->getAlias(),
                        'deaths' => $counter->getDeaths(),
                    ];
                }

                $this->entityManager->persist($counter);
                $this->entityManager->flush();

                $this->producer->publish(\json_encode([
                    'channel' => $user->getUserIdentifier(),
                    'answer' => "{$user->getDisplayName()} has killed '{$counter->getCause()}' and died {$counter->getDeaths()}x",
                ], JSON_THROW_ON_ERROR));

                $id = $tracker->getId();
                $this->hub->publish(new Update("https://simply-stream.com/tracker/${id}", \json_encode([
                    'updated' => $updatedCounter,
                ], JSON_THROW_ON_ERROR)));

                $counters = $this->counterRepository->findByTracker($tracker);
                $total = 0;

                foreach ($counters as $counter) {
                    $total += $counter->getDeaths();
                }

                $this->hub->publish(new Update("https://simply-stream.com/tracker/${id}/total",
                    \json_encode(['total' => $total], JSON_THROW_ON_ERROR)));
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
