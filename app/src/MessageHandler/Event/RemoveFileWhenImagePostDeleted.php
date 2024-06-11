<?php

declare(strict_types=1);

namespace App\MessageHandler\Event;

use App\Message\Event\ImagePostDeletedEvent;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class RemoveFileWhenImagePostDeleted
{
    public function __construct(
        private PhotoFileManager $photoFileManager,
    ) {}

    public function __invoke(ImagePostDeletedEvent $event): void
    {
        $this->photoFileManager->deleteImage($event->filename);
    }
}
