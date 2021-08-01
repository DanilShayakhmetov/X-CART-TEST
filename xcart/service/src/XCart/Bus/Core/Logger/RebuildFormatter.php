<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use XCart\Bus\Query\Data\ScenarioDataSource;

class RebuildFormatter extends XCartFormatter
{
    /**
     * @var ScenarioDataSource
     */
    private $scenarioDataSource;

    /**
     * @param ScenarioDataSource $scenarioDataSource
     */
    public function __construct(
        ScenarioDataSource $scenarioDataSource
    ) {
        parent::__construct();

        $this->scenarioDataSource = $scenarioDataSource;
    }

    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $id = $this->scenarioDataSource->getCurrentScenarioId() ?: 'unknown';

        $output = sprintf(
            "%s\t[%s]\t%s\t%s",
            $record['datetime']->format($this->dateFormat),
            $id,
            $record['level_name'],
            $record['message']
        );

        if ($record['context']) {
            $output .= PHP_EOL;
            foreach ((array) $record['context'] as $key => $value) {
                $output .= 'Context ' . $key . ' => ' . $this->convertToString($value) . PHP_EOL;
            }
        }

        if ($record['extra']) {
            $output .= PHP_EOL;
            foreach ((array) $record['extra'] as $key => $value) {
                $output .= 'Extra ' . $key . ' => ' . $this->convertToString($value) . PHP_EOL;
            }
        }

        return $output . PHP_EOL;
    }
}
