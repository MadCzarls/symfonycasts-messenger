<?php

declare(strict_types=1);

namespace App\Message\Command;

readonly class LogEmoji
{
    public function __construct(
        public int $emojiIndex,
    ) {}
}
