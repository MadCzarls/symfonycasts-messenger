<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

use function sprintf;

class AddPonkaToImageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private PhotoPonkaficator $ponkaficator;
    private PhotoFileManager $photoManager;
    private ImagePostRepository $imagePostRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        PhotoPonkaficator $ponkaficator,
        PhotoFileManager $photoManager,
        ImagePostRepository $imagePostRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->ponkaficator = $ponkaficator;
        $this->photoManager = $photoManager;
        $this->imagePostRepository = $imagePostRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage): void
    {
        $imagePostId = $addPonkaToImage->getImagePostId();
        $imagePost = $this->imagePostRepository->find($imagePostId);

        if (!$imagePost) {
            //could throw an exception but the message would be retried which we don't want here
            //or return and this message will be discarded

            if ($this->logger) {
                // check for unit testing - since for test we will need to call 'setLogger'
                // on this object explicitly
                $this->logger->alert(sprintf('Image post with id %d was missing', $imagePostId));
            }

            return;
        }

        $updatedContents = $this->ponkaficator->ponkafy(
            $this->photoManager->read($imagePost->getFilename())
        );
        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();

        $this->entityManager->persist($imagePost);
        $this->entityManager->flush();
    }
}
