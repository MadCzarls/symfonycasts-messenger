<?php

declare(strict_types=1);

namespace App\Message\Command;

readonly class DeleteImagePost
{
    public function __construct(
        public int $imagePostId,
    ) {}
}
