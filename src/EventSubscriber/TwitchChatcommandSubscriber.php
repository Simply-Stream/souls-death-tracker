<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\TrackerCommandKilledEvent;
use App\Event\TrackerCommandUpdateEvent;
use App\Repository\TrackerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwitchChatcommandSubscriber implements EventSubscriberInterface
{
    protected UserRepository $userRepository;
    protected TrackerRepository $trackerRepository;
    protected EntityManagerInterface $entityManager;
    protected ProducerInterface $producer;

    public function __construct(
        UserRepository $userRepository,
        TrackerRepository $trackerRepository,
        EntityManagerInterface $entityManager,
        ProducerInterface $producer
    ) {
        $this->userRepository = $userRepository;
        $this->trackerRepository = $trackerRepository;
        $this->entityManager = $entityManager;
        $this->producer = $producer;
    }

    /**
     * @throws \JsonException
     */
    public function onTrackerUpdated(TrackerCommandUpdateEvent $event)
    {
        $content = $event->getChatmessage();
        $channel = substr($content['params'][0], 1);
        $command = explode(' ', $content['trailing']);

        if (count($command) < 3) {
            return;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $channel]);
        // @TODO: Change query to something better than the id, something you can actually remember
        $tracker = $this->trackerRepository->findOneBy(['id' => $command[1], 'owner' => $user]);

        if (! $tracker) {
            return;
        }

        if ($tracker->getOwner()->getUserIdentifier() === $channel && (
                $content['userState']['mod'] ||
                in_array('vip/1', $content['userState']['badges'], true))
        ) {
            foreach ($tracker->getSections() as $section) {
                foreach ($section->getDeaths() as $death) {
                    if ($death->getAlias() === $command[2]) {
                        if (isset($command[3])) {
                            $death->setDeat((int)$command[3]);
                        } else {
                            $death->setDeaths($death->getDeaths() + 1);
                        }

                        $this->producer->publish(\json_encode([
                            'channel' => $user->getUserIdentifier(),
                            'answer' => "@{$user->getDisplayName()} has died on '{$death->getCause()}' {$death->getDeaths()}x",
                        ], JSON_THROW_ON_ERROR));
                    }
                }
            }

            $this->entityManager->persist($tracker);
            $this->entityManager->flush();
        }
    }

    public function onTrackerKilled(TrackerCommandKilledEvent $event): void
    {
        $content = $event->getChatmessage();
        $channel = substr($content['params'][0], 1);
        $command = explode(' ', $content['trailing']);

        if (count($command) !== 3) {
            return;
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['username' => $channel]);
        // @TODO: Change query to something better than the id, something you can actually remember
        $tracker = $this->trackerRepository->findOneBy(['id' => $command[1], 'owner' => $user]);

        if (! $tracker) {
            return;
        }

        if ($tracker->getOwner()->getUserIdentifier() === $channel && (
                $content['userState']['mod'] ||
                in_array('vip/1', $content['userState']['badges'], true))
        ) {

            foreach ($tracker->getSections() as $section) {
                foreach ($section->getDeaths() as $death) {
                    if ($death->getAlias() === $command[2]) {
                        $death->setSuccessful(true);

                        $this->producer->publish(\json_encode([
                            'channel' => $user->getUserIdentifier(),
                            'answer' => "{$user->getDisplayName()} has killed '{$death->getCause()}' and died {$death->getDeaths()}x",
                        ], JSON_THROW_ON_ERROR));
                    }
                }
            }

            $this->entityManager->persist($tracker);
            $this->entityManager->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            TrackerCommandUpdateEvent::class => 'onTrackerUpdated',
            TrackerCommandKilledEvent::class => 'onTrackerKilled',
        ];
    }
}
