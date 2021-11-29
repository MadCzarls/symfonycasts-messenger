<?php

declare(strict_types=1);

namespace App\Message\Command;

class LogEmoji
{
    private int $emojiIndex;

    public function __construct(int $emojiIndex)
    {
        $this->emojiIndex = $emojiIndex;
    }

    public function getEmojiIndex(): int
    {
        return $this->emojiIndex;
    }
}
