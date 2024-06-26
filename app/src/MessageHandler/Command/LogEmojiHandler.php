<?php

declare(strict_types=1);

namespace App\MessageHandler\Command;

use App\Message\Command\LogEmoji;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogEmojiHandler
{
    /**
     * @var array|string[]
     */
    private static array $emojis = [
        '¯\_(ツ)_/¯',
        '( ͡° ͜ʖ ͡°)',
        '(>▽<)',
        '(︶︹︺)',
        'へ(⚈益⚈)へ',
    ];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(LogEmoji $logEmoji): void
    {
        $index = $logEmoji->emojiIndex;

        $emoji = self::$emojis[$index] ?? self::$emojis[0];

        $this->logger->info('Important message! ' . $emoji);
    }
}
