<?php

declare(strict_types=1);

namespace App\Message\Command;

class DeleteImagePost
{
    private int $imagePostId;

    public function __construct(int $imagePostId)
    {
        $this->imagePostId = $imagePostId;
    }

    public function getImagePostId(): int
    {
        return $this->imagePostId;
    }
}
