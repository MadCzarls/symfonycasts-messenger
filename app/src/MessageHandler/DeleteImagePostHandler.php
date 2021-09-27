<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\DeleteImagePost;
use App\Photo\PhotoFileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteImagePostHandler implements MessageHandlerInterface
{
    private PhotoFileManager $photoManager;
    private EntityManagerInterface $entityManager;

    public function __construct(PhotoFileManager $photoManager, EntityManagerInterface $entityManager)
    {
        $this->photoManager = $photoManager;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteImagePost $deleteImagePost): void
    {
        $imagePost = $deleteImagePost->getImagePost();
        $this->photoManager->deleteImage($imagePost->getFilename());

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();
    }
}
