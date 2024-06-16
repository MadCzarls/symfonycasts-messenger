<?php

declare(strict_types=1);

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteImagePost;
use App\Message\Event\ImagePostDeletedEvent;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

use function sprintf;

#[AsMessageHandler(handles: DeleteImagePost::class, method: '__invoke', priority: 10)] //fromTransport: 'async' - removed after finishing Chapter 34 od Symfonycast
class DeleteImagePostHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        readonly private MessageBusInterface $eventBus,
        readonly private EntityManagerInterface $entityManager,
        readonly private ImagePostRepository $imagePostRepository,
    ) {}

    public function __invoke(DeleteImagePost $deleteImagePost): void
    {
        $imagePost = $this->imagePostRepository->find($deleteImagePost->imagePostId);

        if (!$imagePost) {
            if ($this->logger) {
                $this->logger->error(
                    sprintf("ImagePost with ID '%d' does not exist", $deleteImagePost->imagePostId)
                );
            }

            return;
        }

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($imagePost->getFilename()));
    }
}
