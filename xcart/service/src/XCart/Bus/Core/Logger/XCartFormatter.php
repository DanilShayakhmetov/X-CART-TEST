<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use Monolog\Formatter\LineFormatter;

class XCartFormatter extends LineFormatter
{
    /**
     * @var string
     */
    protected static $runtimeId;

    /**
     * @param string $dateFormat
     */
    public function __construct()
    {
        parent::__construct("[%datetime%] %runtime_id% %channel%.%level_name%: %message%\n");

        if (empty(static::$runtimeId)) {
            static::$runtimeId = hash('md4', uniqid('runtime', true));
        }
    }

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $record['runtime_id'] = static::$runtimeId;

        $output = parent::format($record);

        if ($record['context']) {
            $output .= 'Context:' . PHP_EOL;
            foreach ((array) $record['context'] as $key => $value) {
                $output .= $key . ' => ' . $this->convertToString($value) . PHP_EOL;
            }
        }

        if ($record['extra']) {
            $output .= 'Extra:' . PHP_EOL;
            foreach ((array) $record['extra'] as $key => $value) {
                $output .= $key . ' => ' . $this->convertToString($value) . PHP_EOL;
            }
        }

        return $output;
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function convertToString($data): string
    {
        if (null === $data || is_scalar($data)) {
            return (string) $data;
        }

        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
