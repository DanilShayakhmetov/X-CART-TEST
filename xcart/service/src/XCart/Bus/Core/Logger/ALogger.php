<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use Exception;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

abstract class ALogger
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $logLevel = LogLevel::INFO;

    /**
     * @param Application $app
     *
     * @return LoggerInterface
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        $self = new static($app['config']['log.path']);
        $self->setLogLevel($app['debug'] ? LogLevel::DEBUG : LogLevel::INFO);

        /** @var Logger|LoggerInterface $log */
        $log = new $app['monolog.logger.class']($self->getName());

        foreach ($self->getHandlers() as $handler) {
            if ($handler) {
                $log->pushHandler($handler);
            }
        }

        return $log;
    }

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * @param string $logLevel
     */
    public function setLogLevel(string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @return FormatterInterface
     */
    protected function getFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }

    /**
     * @return HandlerInterface[]
     */
    private function getHandlers(): array
    {
        return [
            $this->getDefaultHandler(),
        ];
    }

    /**
     * @return PHPFileHandler
     */
    private function getDefaultHandler(): ?PHPFileHandler
    {
        try {
            $handler = new PHPFileHandler($this->getFilePath(), $this->getLogLevel());
            $handler->setFormatter($this->getFormatter());

            return $handler;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    private function getName(): string
    {
        $parts = explode('\\', static::class);

        return 'service-' . strtolower(array_pop($parts));
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        return $this->path . date('/Y/m/') . $this->getName() . '.log.' . date('Y-m-d') . '.php';
    }
}
