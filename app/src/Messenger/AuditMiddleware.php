<?php

declare(strict_types=1);

namespace App\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;

use function get_class;

final class AuditMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $messengerAuditLogger,
    )
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(UniqueIdStamp::class)) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);

        $context = [
            'id' => $stamp->uniqueId,
            'class' => get_class($envelope->getMessage()),
        ];

        $envelope = $stack->next()->handle($envelope, $stack);

        if ($envelope->last(ReceivedStamp::class)) {
            //ReceivedStamp means asynchronous handling - and that message is being received from (after previously being sent to) transport
            $this->messengerAuditLogger->info('[{id}] Received {class}', $context);
        } elseif ($envelope->last(SentStamp::class)) {
            //SentStamp means asynchronous handling - and that message is being sent to (to be later received and handled in) transport
            $this->messengerAuditLogger->info('[{id}] Sent {class}', $context);
        } else {
            //no stamps mean synchronous handling (e.g. in DeleteImagePostHandler) - no routing (transport) is used
            $this->messengerAuditLogger->info('[{id}] Handling sync {class}', $context);
        }

        return $envelope;
    }
}
