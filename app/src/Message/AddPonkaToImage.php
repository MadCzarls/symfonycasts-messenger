<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\ImagePost;

class AddPonkaToImage
{
    private ImagePost $imagePost;

    public function __construct(ImagePost $imagePost)
    {
        $this->imagePost = $imagePost;
    }

    public function getImagePost(): ImagePost
    {
        return $this->imagePost;
    }
}
