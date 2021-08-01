<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use XCart\Marketplace\Logger\Backtrace;
use XCart\Marketplace\Logger\Dumper;

class Logger extends AbstractLogger
{
    /**
     * Current log level
     *
     * @var int
     */
    protected $level = LogLevel::ERROR;

    /**
     * Current log level
     *
     * @var int
     */
    protected $levelWeight = 4;

    /**
     * Append backtrace to any message
     *
     * @var bool
     */
    protected $backtrace = false;

    /**
     * @var array
     */
    protected $pathAliases = [];

    /**
     * Available levels
     *
     * @var array
     */
    protected $levels = [
        LogLevel::EMERGENCY => 1,
        LogLevel::ALERT     => 2,
        LogLevel::CRITICAL  => 3,
        LogLevel::ERROR     => 4,
        LogLevel::WARNING   => 5,
        LogLevel::NOTICE    => 6,
        LogLevel::INFO      => 7,
        LogLevel::DEBUG     => 8,
    ];

    /**
     * @var callable
     */
    protected $writer;

    /**
     * @var callable
     */
    protected $dumper;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param array $options
     */
    public function __construct($options)
    {
        $this->level       = isset($options['level']) ? $options['level'] : LogLevel::ERROR;
        $this->levelWeight = $this->getLevelWeight($this->level);

        $this->writer = isset($options['writer']) && is_callable($options['writer'])
            ? $options['writer']
            : function ($path, $data, $flags) {
                return false !== file_put_contents($path, $data, $flags);
            };

        $this->dumper = isset($options['dumper']) && is_callable($options['dumper'])
            ? $options['dumper']
            : [Dumper::class, 'export'];

        $this->path = isset($options['path']) ? $options['path'] : '';

        $this->backtrace = isset($options['backtrace']) ? (bool) $options['backtrace'] : false;

        $this->pathAliases = isset($options['path_aliases']) ? (array) $options['path_aliases'] : [];
    }

    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->checkLevel($level)) {
            $this->write($level, $message, $context);
        }
    }

    /**
     * @param string $level
     * @param string $message
     * @param mixed  $context
     */
    protected function write($level, $message, $context)
    {
        $writer = $this->writer;

        $writer($this->path, $this->formatMessage($level, $message, $context), \FILE_APPEND);
    }

    /**
     * @param string $level
     * @param string $message
     * @param mixed  $context
     *
     * @return string
     */
    protected function formatMessage($level, $message, $context)
    {
        /** @var \Exception $exception */
        $exception = isset($context['exception']) && $context['exception'] instanceof \Exception
            ? $context['exception']
            : null;

        unset($context['exception']);

        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
            ];

            if (!$message) {
                $message = $exception->getMessage();
            }
        }

        if ($context) {
            $message .= PHP_EOL . var_export($this->prepareData($context), true);
        }

        // if ($this->backtrace || $this->level === LogLevel::DEBUG) {
        if ($this->backtrace) {
            $backtrace = new Backtrace(
                $exception ? $exception->getTrace() : [],
                [
                    'offset'       => 4,
                    'path_aliases' => $this->pathAliases,
                ]
            );

            $message .= PHP_EOL . 'Backtrace:' . PHP_EOL . (string) $backtrace . PHP_EOL;
        }

        return sprintf(
            '[%s] [%s] %s',
            (new \DateTime())->format('H:i:s.u'),
            strtoupper($level),
            $message . PHP_EOL
        );
    }

    /**
     * @param string $level
     *
     * @return bool
     */
    protected function checkLevel($level)
    {
        return $this->getLevelWeight($level) <= $this->levelWeight;
    }

    /**
     * @param string $level
     *
     * @return int
     */
    protected function getLevelWeight($level)
    {
        return isset($this->levels[$level]) ? $this->levels[$level] : 0;
    }

    /**
     * @param mixed $data
     *
     * @return array|\stdClass|string
     */
    protected function prepareData($data)
    {
        if (is_array($data)) {
            foreach ((array) $data as $k => $v) {
                $data[$k] = $this->prepareData($v);
            }
        } else {
            $dumper = $this->dumper;

            $data = $dumper($data, 2);
        }

        return $data;
    }
}
