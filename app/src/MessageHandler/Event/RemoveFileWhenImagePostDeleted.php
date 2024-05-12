<?php

declare(strict_types=1);

namespace App\MessageHandler\Event;

use App\Message\Event\ImagePostDeletedEvent;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveFileWhenImagePostDeleted
{
    private PhotoFileManager $photoFileManager;

    public function __construct(PhotoFileManager $photoFileManager)
    {
        $this->photoFileManager = $photoFileManager;
    }

    public function __invoke(ImagePostDeletedEvent $event): void
    {
        $this->photoFileManager->deleteImage($event->filename);
    }
}
