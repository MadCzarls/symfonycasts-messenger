<?php

declare(strict_types=1);

namespace App\Messenger;

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
     * @param array|mixed[] $encodedEnvelope
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];
        $headers = $encodedEnvelope['headers'];

        $data = json_decode($body, true);

        if ($data === null) {
            throw new MessageDecodingFailedException('Invalid JSON');
        }

        if (!isset($headers['type'])) {
            throw new MessageDecodingFailedException('Missing "type" header');
        }

        switch ($headers['type']) {
            case 'emoji':
                $envelope = $this->createLogEmojiEnvelope($data);
                break;
            default:
                throw new MessageDecodingFailedException(sprintf('Invalid type "%s"', $headers['type']));
        }

        // in case of redelivery, unserialize any stamps
        $stamps = [];
        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        return $envelope->with(...$stamps);
    }

    /**
     * This function is called when WRITEing to transport so it should not be needed since we only want to READ from it
     * - but it's also used during 'retry' (redelivery) - during READing from transport - so we have to implement it
     *
     * @return mixed[]
     *
     * @throws Exception
     */
    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if (!($message instanceof LogEmoji)) {
            throw new Exception('Unsupported message class');
        }

        // recreate what the data originally looked like
        $data = ['emoji' => $message->emojiIndex];
        $type = 'emoji';

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

    public function createLogEmojiEnvelope(array $data): Envelope
    {
        if (!isset($data['emoji'])) {
            throw new MessageDecodingFailedException('Missing the emoji key!');
        }

        $message = new LogEmoji($data['emoji']);
        $envelope = new Envelope($message);

        return $envelope->with(new BusNameStamp('command.bus')); // needed only if you need this to be sent through the non-default bus
    }
}
