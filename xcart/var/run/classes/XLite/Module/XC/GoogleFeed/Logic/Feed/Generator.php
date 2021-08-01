<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed;

use Includes\Utils\FileManager;
use XLite\Core\Database;
use XLite\Core\EventListener;
use XLite\Core\Lock\FileLock;
use XLite\Logic\AGenerator;

/**
 * Generator
 */
class Generator extends AGenerator
{
    const TTL                   = 604800;

    /**
     * Record
     *
     * @var string
     */
    protected $record;

    /**
     * Is page has alternative language url
     *
     * @var boolean
     */
    protected $hasAlternateLangUrls;

    /**
     * @var \XLite\Module\XC\GoogleFeed\Core\Task\FeedUpdater
     */
    protected $feedUpdater;

    /**
     * Check if store has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        if (null === $this->hasAlternateLangUrls) {
            $router = \XLite\Core\Router::getInstance();
            $this->hasAlternateLangUrls = LC_USE_CLEAN_URLS
                && $router->isUseLanguageUrls()
                && count($router->getActiveLanguagesCodes()) > 1;
        }

        return $this->hasAlternateLangUrls;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }

        $this->setRecord($this->getRecord() . $this->getFooter());
        $this->flushRecord();
        $this->removeFeedFiles();
        $this->moveFeeds();
    }

    /**
     * Get process tick duration
     *
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = Database::getRepo('XLite\Model\TmpVar')
                ->getVar(static::getTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ?: static::DEFAULT_TICK_DURATION;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        if (!FileManager::isExists(LC_DIR_DATA)) {
            FileManager::mkdir(LC_DIR_DATA);
            if (!FileManager::isExists(LC_DIR_DATA)) {
                $message = 'The directory ' . LC_DIR_DATA . ' can not be created. Check the permissions to create directories.';

                \XLite\Logger::getInstance()->log($message, LOG_ERR);

                $this->addError('Directory permissions', $message);

                static::cancel();
            }
        }

        $this->removeTemporaryFeedFiles();
        $this->setFileIndex(1);
        $this->setRecord($this->getHead());
    }

    // {{{ Steps

    /**
     * Return steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        return [
            'XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products',
        ];
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return $this->getStepsList();
    }

    /**
     * Process steps
     *
     * @return void
     */
    protected function processSteps()
    {
        if ($this->getOptions()->include) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->include)) {
                    unset($this->steps[$i]);
                }
            }
        }

        $steps = $this->steps;
        $this->steps = [];
        foreach ($steps as $step) {
            if (\XLite\Core\Operator::isClassExists($step)) {
                $this->steps[] = new $step($this);

                if ($this->hasAlternateLangUrls()) {
                    foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                        $this->steps[] = new $step($this, $language->getCode());
                    }
                }
            }
        }

        $this->steps = array_values($this->steps);
    }

    /**
     * Define current step
     *
     * @return integer
     */
    protected function defineStep()
    {
        $currentStep = null;

        if (!Database::getRepo('XLite\Model\TmpVar')->getVar(static::getCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);
                    break;

                } else {
                    $i -= $step->count();
                }
            }
        }

        return $currentStep;
    }

    // }}}

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count'] += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Error / warning routines

    /**
     * Add error
     *
     * @param string $title Title
     * @param string $body  Body
     *
     * @return void
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = array(
            'title' => $title,
            'body' => $body,
        );
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return 'feedGenerationTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return 'feedGenerationCancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'feedGeneration';
    }

    // }}}


    // {{{ File operations

    /**
     * Return head
     *
     * @return string
     */
    protected function getHead()
    {
        $updated = date('Y-m-d', LC_START_TIME) . 'T' . date('H:i:s', LC_START_TIME) . 'Z';

        return <<<HEAD
<?xml version="1.0" encoding="UTF-8" ?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
<title>X-Cart Product Feed</title>
<updated>{$updated}</updated>
<lastBuildDate>{$updated}</lastBuildDate>
HEAD;
    }

    /**
     * Return footer
     *
     * @return string
     */
    protected function getFooter()
    {
        return '</feed>';
    }

    /**
     * Return current file index
     *
     * @return int
     */
    protected function getFileIndex()
    {
        return $this->getOptions()->fileIndex;
    }

    /**
     * Set current file index
     *
     * @param integer $index
     */
    protected function setFileIndex($index)
    {
        $this->getOptions()->fileIndex = $index;
    }

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getPrefix()
    {
        return 'tmp_';
    }

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getFilenamePart()
    {
        return 'googlefeed';
    }

    /**
     * Return array of temporary files
     *
     * @return array
     */
    protected function getTemporaryFeedFiles()
    {
        return glob(LC_DIR_DATA . static::getPrefix() . static::getFilenamePart() . '.*.xml') ?: [];
    }

    /**
     * Remove temporary files
     */
    protected function removeTemporaryFeedFiles()
    {
        foreach ($this->getTemporaryFeedFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Return array of previously generated sitemap files
     *
     * @return array
     */
    protected function getFeedFiles()
    {
        return glob(LC_DIR_DATA . static::getFilenamePart() . '.*.xml') ?: [];
    }

    /**
     * Get sitemap by index
     *
     * @param integer $index Index
     *
     * @return string
     */
    public function getFeed($index = 1)
    {
        $path = LC_DIR_DATA . static::getFilenamePart() . '.' . $index . '.xml';

        return \Includes\Utils\FileManager::isExists($path) ? file_get_contents($path) : null;
    }


    /**
     * Remove temporary files
     */
    protected function removeFeedFiles()
    {
        foreach ($this->getFeedFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Move feed files
     *
     * @return void
     */
    public function moveFeeds()
    {
        $sep = preg_quote(LC_DS, '/');
        $prefix = preg_quote($this->getPrefix(), '/');
        foreach ($this->getTemporaryFeedFiles() as $path) {
            $to = preg_replace('/^(.+' . $sep . ')' . $prefix . '(' . static::getFilenamePart() . '\..*\.xml)$/', '\\1\\2', $path);
            FileManager::move($path, $to);
        }
    }

    /**
     * Get feed path
     *
     * @return string
     */
    protected function getCurrentTemporaryFeedPath()
    {
        return LC_DIR_DATA . static::getPrefix() . static::getFilenamePart() . '.' . $this->getFileIndex() . '.xml';
    }

    /**
     * Return Record
     *
     * @return string
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set Record
     *
     * @param string $record
     *
     * @return $this
     */
    public function setRecord($record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Build location URL
     *
     * @param array $loc Locationb as array
     *
     * @return string
     */
    protected function buildLoc(array $loc)
    {
        $target = $loc['target'];
        unset($loc['target']);

        return \XLite\Core\Converter::buildFullURL($target, '', $loc, \XLite::getCustomerScript(), true);
    }

    /**
     * Add sitemap item to record
     *
     * @param array $item
     *
     * @return $this
     */
    public function addToRecord(array $item)
    {
        $string = '<entry>';

        foreach ($item as $tag => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_array($value)) {
                $string .= '<' . $tag . '>';
                foreach ($value as $subtag => $entry) {
                    $string .= '<' . $subtag . '>' . htmlspecialchars($entry) . '</' . $subtag . '>';
                }
                $string .= '</' . $tag . '>';
            } else {
                if ($tag == 'g:additional_image_link') {
                    $string .= '<' . $tag . '>' . $value . '</' . $tag . '>';
                } else {
                    $string .= '<' . $tag . '>' . htmlspecialchars($value) . '</' . $tag . '>';
                }
            }
        }

        $string .= '</entry>';

        $this->setRecord($this->getRecord() . $string);

        return $this;
    }

    /**
     * Write record
     *
     * @return void
     */
    public function flushRecord()
    {
        if ($this->getRecord()) {
            FileManager::write($this->getCurrentTemporaryFeedPath(), $this->getRecord(), FILE_APPEND);
            $this->setRecord('');
        }
    }

    // }}}

    // {{{

    /**
     * Check - feed files generated or not
     *
     * @return boolean
     */
    public function isGenerated()
    {
        $list = glob(LC_DIR_DATA . static::getFilenamePart() .'.*.xml');

        return $list && 0 < count($list);
    }

    /**
     * Check - sitemap file is obsolete or not
     *
     * @param integer $ttl TTL OPTIONAL
     *
     * @return boolean
     */
    public function isObsolete($ttl = self::TTL)
    {
        $time = null;

        $list = glob(LC_DIR_DATA . static::getFilenamePart() . '.*.xml');

        if ($list) {
            foreach ($list as $path) {
                $time = $time ? min($time, filemtime($path)) : filemtime($path);
            }
        }

        return $time && $time + $ttl < \XLite\Core\Converter::time();
    }

    /**
     * Generate feed in headless mode
     *
     * @return bool
     */
    public function generate()
    {
        static::run([]);
        static::lock();
        $event = static::getEventName();

        do {
            \XLite\Module\XC\GoogleFeed\Core\EventListener\FeedGeneration::getInstance()->unsetGenerator();
            $em = \XLite\Core\Database::getEM();
            $em->clear();

            if (isset($this->feedUpdater)) {
                $this->feedUpdater->mergeModel();
            }

            $result = EventListener::getInstance()->handle($event, array());

            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($event);

            if ($state['state'] === \XLite\Core\EventTask::STATE_FINISHED
                || $state['state'] === \XLite\Core\EventTask::STATE_ABORTED) {
                $result = false;
            }

        } while ($result);

        $errors = EventListener::getInstance()->getErrors();

        if ($errors) {
            $result = false;
        }

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getRepo('XLite\Model\EventTask')->cleanTasks($event, 0);
        static::unlock();

        return $result;
    }

    /**
     * @param $feedUpdater
     */
    public function setFeedUpdater($feedUpdater)
    {
        $this->feedUpdater = $feedUpdater;
    }

    // }}}
}
