<?php

declare(strict_types=1);

namespace App\Photo;

use App\Entity\ImagePost;
use Exception;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function fclose;
use function fopen;
use function is_resource;
use function pathinfo;
use function sprintf;
use function uniqid;

use const PATHINFO_FILENAME;

class PhotoFileManager
{
    private FilesystemOperator $filesystem;
    private string $publicAssetBaseUrl;

    public function __construct(FilesystemOperator $photoFilesystem, string $publicAssetBaseUrl)
    {
        $this->filesystem = $photoFilesystem;
        $this->publicAssetBaseUrl = $publicAssetBaseUrl;
    }

    public function uploadImage(File $file): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();
        $stream = fopen($file->getPathname(), 'r');

        try {
            $this->filesystem->writeStream(
                $newFilename,
                $stream,
                [
                    'visibility' => Visibility::PUBLIC,
                ]
            );
        } catch (\Throwable) {
            throw new Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    public function deleteImage(string $filename): void
    {
        $this->filesystem->delete($filename);
    }

    public function getPublicPath(ImagePost $imagePost): string
    {
        return $this->publicAssetBaseUrl . '/' . $imagePost->getFilename();
    }

    public function read(string $filename): string
    {
        return $this->filesystem->read($filename);
    }

    public function update(string $filename, string $updatedContents): void
    {
        $this->filesystem->update($filename, $updatedContents);
    }
}
