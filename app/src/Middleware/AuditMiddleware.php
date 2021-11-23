<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stamp\UniqueIdStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;

use function get_class;

class AuditMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $messengerAuditLogger)
    {
        $this->logger = $messengerAuditLogger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(UniqueIdStamp::class) === null) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);

        $context = [
            'id' => $stamp->getUniqueId(),
            'class' => get_class($envelope->getMessage()),
        ];

        $envelope = $stack->next()->handle($envelope, $stack);

        if ($envelope->last(SentStamp::class)) {
            //SentStamp means asynchronous handling - and that message is being sent to (to be later received and handled in) transport
            $this->logger->info('[{id}] Sent to transport {class}', $context);
        } elseif ($envelope->last(ReceivedStamp::class)) {
            //ReceivedStamp means asynchronous handling - and that message is being received from (after previously sent to) transport
            $this->logger->info('[{id}] Received from transport {class}', $context);
        } else {
            //no stamps mean synchronous handling (DeleteImagePostHandler) - no routing (transport) is used
            $this->logger->info('[{id}] Handling sync {class}', $context);
        }

        return $envelope;
    }
}
