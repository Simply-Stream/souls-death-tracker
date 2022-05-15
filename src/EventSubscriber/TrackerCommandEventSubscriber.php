<?php

namespace SimplyStream\SoulsDeathBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use SimplyStream\SoulsDeathBundle\Entity\Counter;
use SimplyStream\SoulsDeathBundle\Entity\Section;
use SimplyStream\SoulsDeathBundle\Event\AddCauseCommandSuccess;
use SimplyStream\SoulsDeathBundle\Event\AddSectionCommandSuccess;
use SimplyStream\SoulsDeathBundle\Event\CommandExecutionEvent;
use SimplyStream\SoulsDeathBundle\Event\CommandExecutionSuccessEvent;
use SimplyStream\SoulsDeathBundle\Event\SendChatmessageEvent;
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
     *
     * @TODO: Implement a DTO
     */
    public function onCommandExecutionEvent(CommandExecutionEvent $event)
    {
        $chatmessage = $event->getChatMessage();
        $user = $this->userRepository->findOneBy(['twitchId' => $chatmessage['RoomId']]);
        $tracker = $this->trackerRepository->findOneBy(['commandName' => $event->getCommand(), 'owner' => $user]);

        if ($tracker && $user && ($chatmessage['IsBroadcaster'] || $chatmessage['IsMod'] || $chatmessage['isVip'])) {
            $channel = $event->getChannel();
            $command = str_getcsv(substr($event->getChatMessage()['Message'], 1), " ");

            if (count($command) === 1) {
                return;
            }

            if (count($command) > 1) {
                switch (strtolower($command[1])) {
                    case '?':
                    case 'help':
                        $this->sendHelpChatMessage($command, $channel);
                        break;
                    case 'addsection':
                        // Add a new section to tracker
                        $sectionName = $command[2];
                        $newSection = (new Section())->setTitle($sectionName);

                        $tracker->addSection($newSection);

                        $this->entityManager->persist($tracker);
                        $this->entityManager->flush();

                        $message = "Section '${sectionName}' has been added to '!${command[0]}'";
                        $event = new SendChatmessageEvent($channel, $message);
                        $this->eventDispatcher->dispatch($event);

                        $event = new AddSectionCommandSuccess($newSection, $channel);
                        $this->eventDispatcher->dispatch($event);

                        break;
                    case 'addcause':
                        // Add new cause to section. Don't use IDs for section, use number of order (1 = maingame, 2 = first dlc, etc)
                        if (count($command) < 4 || ! is_numeric($command[2])) {
                            return;
                        }

                        $sectionOrderNumber = $command[2];
                        $causeName = $command[3];
                        $causeAlias = strtolower(str_replace(' ', '-', $causeName));
                        $deaths = 0;

                        try {
                            $section = $tracker->getSections()[$sectionOrderNumber];
                        } catch (\Exception $exception) {
                            // @TODO: Log error
                            // Just die silent if section does not exist
                            return;
                        }

                        if (isset($command[4])) {
                            if (is_numeric($command[4])) {
                                $deaths = $command[4];
                            } else {
                                $causeAlias = $command[4];
                            }
                        }

                        if (isset($command[5])) {
                            $deaths = $command[5];
                        }

                        $death = (new Counter())
                            ->setSection($section)
                            ->setCause($causeName)
                            ->setAlias($causeAlias)
                            ->setDeaths($deaths);

                        $this->entityManager->persist($death);
                        $this->entityManager->flush();

                        $message = "Death cause '${causeName}' with alias '${causeAlias}' and ${deaths} has been added to '!${command[0]}'";
                        $event = new SendChatmessageEvent($channel, $message);
                        $this->eventDispatcher->dispatch($event);

                        $event = new AddCauseCommandSuccess($death, $channel);
                        $this->eventDispatcher->dispatch($event);

                        break;
                    case 'search':
                        // Search for aliases in case user can't remember the name
                        break;
                    case 'aliases':
                        // List all aliases
                        break;
                    default:
                        // Defaults to changing value of a death counter
                        $counter = $this->counterRepository->findOneByAliasInTracker($command[1], $tracker);

                        if (! $counter) {
                            return;
                        }

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
    }

    protected function sendHelpChatMessage(array $command, string $channel): void
    {
        $commandName = $command[0];

        if (count($command) === 3) {
            // 0 = command, 1 = help|?, 2 = command
            $message = match ($command[2]) {
                'addSection' => "Usage: !${commandName} addSection <sectionName>",
                'addCause' => "Usage: !${commandName} addCause <sectionOrder> <deathCauseName> [<deathCauseAlias> and/or <deathCauseValue>].",
                'search' => "Usage: !${commandName} search <deathCauseName>. Fuzzy search for alias of death cause.",
                'list' => "Usage: !${commandName} list. Whispers the list of all aliases and names of a tracker.",
                default => null
            };
        } else {
            $message = "Usage: !${commandName} <alias> [+<number>|-<number>|=<number>]. <number> = Optional value that changes death counter. Will add 1 to counter if empty. Prefix with -, + or = to subtract from, add to or set death counter to <number>.";
        }

        if ($message) {
            $event = new SendChatmessageEvent($channel, $message);
            $this->eventDispatcher->dispatch($event);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandExecutionEvent::class => 'onCommandExecutionEvent',
        ];
    }
}
