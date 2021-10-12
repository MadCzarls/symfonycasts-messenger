<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\DeleteImagePost;
use App\Photo\PhotoFileManager;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use function sprintf;

class DeleteImagePostHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    private PhotoFileManager $photoManager;
    private EntityManagerInterface $entityManager;
    private ImagePostRepository $imagePostRepository;

    public function __construct(
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository,
    ) {
        $this->photoManager = $photoManager;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(DeleteImagePost $deleteImagePost): void
    {
        $imagePost = $this->imagePostRepository->find($deleteImagePost->getImagePostId());

        if (!$imagePost) {
            if ($this->logger) {
                $this->logger->error(
                    sprintf("ImagePost with ID '%d' does not exist", $deleteImagePost->getImagePostId())
                );
            }

            return;
        }

        $this->photoManager->deleteImage($imagePost->getFilename());

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();
    }
}
