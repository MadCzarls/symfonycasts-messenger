<?php

declare(strict_types=1);

namespace App\MessageHandler\Query;

use App\Message\Query\GetTotalImageCount;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetTotalImageCountHandler implements MessageHandlerInterface
{
    public function __invoke(GetTotalImageCount $getTotalImageCount): int
    {
        //@TODO
        return 50;
    }
}
