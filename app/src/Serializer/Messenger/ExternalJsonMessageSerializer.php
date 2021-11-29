<?php

declare(strict_types=1);

namespace App\Serializer\Messenger;

use App\Message\Command\LogEmoji;
use Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

use function array_merge;
use function json_decode;
use function json_encode;
use function serialize;
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
        $message = new LogEmoji($data['emoji']);

        // in case of redelivery, unserialize any stamps
        $stamps = [];
        if (isset($headers['stamps'])) {
            $stamps = unserialize($headers['stamps']);
        }

        return new Envelope($message, $stamps);
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

        if (!($message instanceof LogEmoji)) { //@TODO extend if to handle more messages' classes when needed
            throw new Exception('Unsupported message class');
        }

        $data = ['emoji' => $message->getEmojiIndex()];

        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }

        return [
            'body' => json_encode($data),
            'headers' => [
                //store stamps as header - to be read in decode()
                'stamps' => serialize($allStamps),
            ],
        ];
    }
}
