<?php

declare(strict_types=1);

namespace App\Serializer\Messenger;

use App\Message\Command\LogEmoji;
use Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

use function array_merge;
use function json_decode;
use function json_encode;
use function serialize;
use function sprintf;
use function unserialize;

class ExternalJsonMessageSerializer implements SerializerInterface
{
    /**
     * @param array|mixed[] $data
     */
    private function createLogEmojiEnvelope(array $data): Envelope
    {
        if (!isset($data['emoji'])) {
            throw new MessageDecodingFailedException('Missing the emoji key!');
        }

        $message = new LogEmoji($data['emoji']);
        $envelope = new Envelope($message);

        // needed only if you need this to be sent through the non-default bus
        $envelope = $envelope->with(new BusNameStamp('command.bus'));

        return $envelope;
    }

    /**
     * @param array|mixed[] $encodedEnvelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        if (!isset($headers['type'])) {
            throw new MessageDecodingFailedException('Missing "type" header');
        }

        $data = json_decode($body, true);

        if ($data === null) {
            throw new MessageDecodingFailedException('Invalid JSON');
        }

        $envelope = match ($headers['type']) {
            'emoji' => $this->createLogEmojiEnvelope($data),
            default => throw new MessageDecodingFailedException(sprintf('Invalid type "%s"', $headers['type'])),
        };

        // in case of redelivery, unserialize any stamps
        $stamps = [];
        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        $envelope = $envelope->with(...$stamps);

        return $envelope;
    }

    /**
     * This function is called when WRITEing to transport so it should not be needed since we onlye want to READ from it
     * - but it's also used during 'retry' (redelivery) - during READing from transport - we have to implement it
     *
     * @return mixed[]
     *
     * @throws Exception
     */
    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        if ($message instanceof LogEmoji) {
            $type = 'emoji';
        } else {
            throw new MessageDecodingFailedException('Not supported type');
        }

        $data = ['emoji' => $message->getEmojiIndex()];
        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }

        return [
            'body' => json_encode($data),
            'headers' => [//store stamps as header - to be read in decode()
                'stamps' => serialize($allStamps),
                'type' => $type,
            ],
        ];
    }
}
