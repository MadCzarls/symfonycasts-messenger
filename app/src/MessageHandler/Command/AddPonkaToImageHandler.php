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
        readonly private PhotoPonkaficator $ponkaficator,
        readonly private PhotoFileManager $photoManager,
        readonly private ImagePostRepository $imagePostRepository,
        readonly private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage): void
    {
        $imagePost = $this->imagePostRepository->find($addPonkaToImage->imagePostId);

        if (!$imagePost) {
            //could throw an exception but the message would be retried which we don't want here
            //or return and this message will be discarded

            if ($this->logger) {
                // check for unit testing - since for test we will need to call 'setLogger'
                // on this object explicitly
                $this->logger->alert(sprintf('Image post with id %d was missing', $addPonkaToImage->imagePostId));
            }

            return;
        }

//        Commenting this at the end of Chapter 21 to be able to follow Symfonycast smoothly ;)
//        if (rand(0, 10)< 7) {
//            throw new \Exception('I failed randomly!!!!');
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
