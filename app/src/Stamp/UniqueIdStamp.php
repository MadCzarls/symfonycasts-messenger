<?php

declare(strict_types=1);

namespace App\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

use function uniqid;

class UniqueIdStamp implements StampInterface
{
    private string $uniqueId;

    public function __construct()
    {
        $this->uniqueId = uniqid();
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }
}