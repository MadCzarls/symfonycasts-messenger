<?php

declare(strict_types=1);

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteImagePost;
use App\Message\Event\ImagePostDeletedEvent;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use function sprintf;

class DeleteImagePostHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MessageBusInterface $eventBus;
    private EntityManagerInterface $entityManager;
    private ImagePostRepository $imagePostRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository,
    ) {
        $this->eventBus = $messageBus;
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

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($imagePost->getFilename()));
    }
}