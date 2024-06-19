<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ImagePost;
use App\Message\Command\AddPonkaToImage;
use App\Message\Command\DeleteImagePost;
use App\Message\Query\GetTotalImageCount;
use App\Photo\PhotoFileManager;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function count;

class ImagePostController extends AbstractController
{
    #[Route('/api/images-count', methods: ['GET'])]
    public function count(MessageBusInterface $queryBus): JsonResponse
    {
        $envelope = $queryBus->dispatch(new GetTotalImageCount());

        /** @var HandledStamp $handled */
        $handled = $envelope->last(HandledStamp::class);

        $posts = $handled->getResult();

        return $this->toJson(['items' => $posts]);
    }

    #[Route('/api/images', methods: ['GET'])]
    public function list(ImagePostRepository $repository): JsonResponse
    {
        $posts = $repository->findBy([], ['createdAt' => 'DESC']);

        return $this->toJson(['items' => $posts]);
    }

    #[Route('/api/images', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        MessageBusInterface $commandBus
    ): JsonResponse {
//        $commandBus->dispatch(new \App\Message\Command\LogEmoji(2)); // commented out after finishing Chapter 43 of Symfonycast

        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('file');

        $errors = $validator->validate($imageFile, [
            new Image(),
            new NotBlank(),
        ]);

        if (count($errors) > 0) {
            return $this->toJson($errors, 400);
        }

        $newFilename = $photoManager->uploadImage($imageFile);
        $imagePost = new ImagePost();
        $imagePost->setFilename($newFilename);
        $imagePost->setOriginalFilename($imageFile->getClientOriginalName());

        $entityManager->persist($imagePost);
        $entityManager->flush();

        $message = new AddPonkaToImage($imagePost->getId());
        $envelope = new Envelope($message, [
//            new DelayStamp(500), // commented out after finishing Chapter 35 of Symfonycast
//            new DelayStamp(60000), // commented out after finishing Chapter 38 of Symfonycast
            new DelayStamp(1000),
            new AmqpStamp('normal'),
        ]);

        dump($commandBus->dispatch($envelope));

        return $this->toJson($imagePost, 201);
    }

    #[Route('/api/images/{id}', methods: ['DELETE'])]
    public function delete(
        ImagePost $imagePost,
        MessageBusInterface $commandBus
    ): Response {
        $message = new DeleteImagePost($imagePost->getId());
        $commandBus->dispatch($message);

        return new Response(null, 204);
    }

    #[Route('/api/images/{id}', name: 'get_image_post_item', methods: ['GET'])]
    public function getItem(ImagePost $imagePost): JsonResponse
    {
        return $this->toJson($imagePost);
    }

    /**
     * @param string[] $headers
     * @param mixed[]  $context
     */
    private function toJson(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        // add the image:output group by default
        if (!isset($context['groups'])) {
            $context['groups'] = ['image:output'];
        }

        return $this->json($data, $status, $headers, $context);
    }
}
