<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ImagePostControllerTest extends WebTestCase
{
    public function testCreate(): void
    {
        $client = static::createClient();

        $uploadedFile = new UploadedFile(
            __DIR__.'/../fixtures/cat1.jpg',
            'cat1.jpg'
        );

        $client->request('POST', '/api/images', [], [
           'file' => $uploadedFile
        ]);

        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.async_priority_high');

        $this->assertCount(1, $transport->get());

        $this->assertResponseIsSuccessful();
    }
}
