<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\ImagePost;
use App\Photo\PhotoFileManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class ImagePostNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private PhotoFileManager $uploaderManager,
        private UrlGeneratorInterface $router
    ) {
    }

    /**
     * @param ImagePost     $imagePost
     * @param string|null   $format
     * @param array|mixed[] $context
     *
     * @return mixed[]
     *
     * @throws ExceptionInterface
     */
    public function normalize($imagePost, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($imagePost, $format, $context);

        // a custom, and therefore "poor" way of adding a link to myself
        // formats like JSON-LD (from API Platform) do this in a much
        // nicer and more standardized way
        $data['@id'] = $this->router->generate('get_image_post_item', [
            'id' => $imagePost->getId(),
        ]);
        $data['url'] = $this->uploaderManager->getPublicPath($imagePost);

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ImagePost;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ImagePost::class => true,
        ];
    }
}
