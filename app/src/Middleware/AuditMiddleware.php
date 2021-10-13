<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Stamp\UniqueIdStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

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

        //@TODO log message but only on dispatching, not retrieving from transport (middlewares are triggered in both situations)

        return $stack->next()->handle($envelope, $stack);
    }
}
