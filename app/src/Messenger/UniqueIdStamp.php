<?php

declare(strict_types=1);

namespace App\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

use function uniqid;

readonly class UniqueIdStamp implements StampInterface
{
    public string $uniqueId;

    public function __construct()
    {
        $this->uniqueId = uniqid();
    }
}
