<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\ImagePost;
use App\Photo\PhotoFileManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ImagePostNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private ObjectNormalizer $normalizer;
    private PhotoFileManager $uploaderManager;
    private UrlGeneratorInterface $router;

    public function __construct(
        ObjectNormalizer $normalizer,
        PhotoFileManager $uploaderManager,
        UrlGeneratorInterface $router
    ) {
        $this->normalizer = $normalizer;
        $this->uploaderManager = $uploaderManager;
        $this->router = $router;
    }

    /**
     * @param ImagePost   $imagePost
     * @param string|null $format
     * @param mixed[]     $context
     *
     * @return mixed[]
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

    /**
     * @param mixed       $data
     * @param string|null $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ImagePost;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
