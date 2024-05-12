<?php

declare(strict_types=1);

namespace App\Message\Event;

readonly class ImagePostDeletedEvent
{
    public function __construct(
        public string $filename,
    ) {}
}
