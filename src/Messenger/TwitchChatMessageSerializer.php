<?php

namespace SimplyStream\SoulsDeathBundle\Messenger;

use SimplyStream\SoulsDeathBundle\Message\TwitchChatCommandMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as Serializer;

class TwitchChatMessageSerializer implements SerializerInterface
{
    protected Serializer $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws \JsonException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        return new Envelope($this->serializer->deserialize($encodedEnvelope['body'], TwitchChatCommandMessage::class, 'json'));
    }

    public function encode(Envelope $envelope): array
    {
        return [
            'body' => $this->serializer->serialize($envelope->getMessage(), 'json'),
            'header' => null,
        ];
    }
}
