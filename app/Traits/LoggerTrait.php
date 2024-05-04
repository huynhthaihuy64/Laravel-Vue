<?php

namespace App\Traits;

use App\Constants\Constants;
use GGG\Log\Logger;
use Psr\Log\LogLevel;
use Throwable;

trait LoggerTrait
{
    /**
     * @param string $message
     * @param array $context
     * @param string|null $channelName
     * @param string $level
     * @return void
     */
    public static function writeLog(string $message, array $context = [], string $channelName = null, string $level = LogLevel::INFO): void
    {
        $message .= !empty($context) ? " " . json_encode($context, JSON_UNESCAPED_UNICODE) : "";
        $logger = new Logger(null, Constants::PROJECT_NAME);

        $logger->write(
            $channelName ?? config('logging.default'),
            $message,
            true,
            $level
        );
    }

    /**
     * @param string $message
     * @param string|array $context
     * @param string|null $channelName
     * @return void
     */
    public static function writeLogDebug(string $message, array $context = [], string $channelName = null): void
    {
        self::writeLog($message, $context, $channelName, LogLevel::DEBUG);
    }

    /**
     * @param Exception $exception
     * @param string|null $channelName
     * @return void
     */
    public static function writeLogError(
        Throwable $exception,
        string $channelName = null
    ): void {
        $logger = new Logger(null, Constants::PROJECT_NAME);

        $logger->write(
            $channelName ?? config('logging.default'),
            $exception,
            false,
            LogLevel::ERROR
        );
    }
}
