<?php

declare(strict_types=1);

namespace App\MessageHandler\Command;

use App\Message\Command\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use function sprintf;

#[AsMessageHandler]
class AddPonkaToImageHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private PhotoPonkaficator $ponkaficator,
        private PhotoFileManager $photoManager,
        private ImagePostRepository $imagePostRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage): void
    {
        $imagePostId = $addPonkaToImage->imagePostId;
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

//        if (rand(0, 10)< 7 || true) {
//            throw new \Exception('I failed randomly!!');
//        }

        $updatedContents = $this->ponkaficator->ponkafy(
            $this->photoManager->read($imagePost->getFilename())
        );
        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();

        $this->entityManager->persist($imagePost);
        $this->entityManager->flush();
    }
}
