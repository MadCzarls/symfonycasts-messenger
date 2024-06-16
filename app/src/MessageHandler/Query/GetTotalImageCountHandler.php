<?php

declare(strict_types=1);

namespace App\MessageHandler\Query;

use App\Message\Query\GetTotalImageCount;
use App\Repository\ImagePostRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetTotalImageCountHandler
{
    public function __construct(
        private ImagePostRepository $imagePostRepository,
    ) {
    }

    public function __invoke(GetTotalImageCount $getTotalImageCount): int
    {
        return $this->imagePostRepository->count([]);
    }
}
