<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\ImagePostController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagePostControllerTest extends WebTestCase
{

    public function testCreate(): void
    {
        $client = $this->createClient();

        $uploadedFile = new UploadedFile(
            __DIR__.'/../fixtures/cat1.jpg',
            'cat1.jpg'
        );

        $client->request('POST', '/api/images', [], [
           'file' => $uploadedFile
        ]);

        $this->assertResponseIsSuccessful();
    }
}
