<?php

declare(strict_types=1);

namespace App\MessageHandler\Query;

use App\Message\Query\GetTotalImageCount;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetTotalImageCountHandler
{
    public function __invoke(GetTotalImageCount $getTotalImageCount): int
    {
        //@TODO
        return 50;
    }
}
