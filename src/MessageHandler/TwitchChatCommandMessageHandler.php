<?php

namespace App\MessageHandler;

use App\Event\CommandExecutionEvent;
use App\Message\TwitchChatCommandMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class TwitchChatCommandMessageHandler implements MessageHandlerInterface
{
    public const CHAT_COMMAND_TRACKER_NAME = 'tracker';
    public const CHAT_COMMAND_SUCCESS_NAME = 'killed';

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(TwitchChatCommandMessage $message)
    {
        $content = $message->getMessage();
        $channel = substr($content['params'][0], 1);
        $isCommand = str_starts_with($content['trailing'], ':!');

        if ($channel && $isCommand) {
            $command = substr(explode(' ', $content['trailing'])[0], 2);
            $this->eventDispatcher->dispatch(new CommandExecutionEvent($command, $channel, $content));
        }
    }
}
