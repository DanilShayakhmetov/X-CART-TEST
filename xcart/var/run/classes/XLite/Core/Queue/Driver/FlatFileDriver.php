<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Driver;

/**
 * Fork of \Bernard\Driver\FlatFileDriver
 * The only difference is order
 *   \Bernard\Driver\FlatFileDriver is LIFO
 *   This FlatFileDriver is FIFO
 *
 * upd: peekQueue function also modified
 */
class FlatFileDriver implements \Bernard\Driver
{
    private $baseDirectory;

    private $permissions;

    private $currentQueueSubdir;

    /**
     * @param string $baseDirectory The base directory
     * @param int    $permissions   Permissions to create the file with.
     */
    public function __construct($baseDirectory, $permissions = 0740)
    {
        $this->baseDirectory = $baseDirectory;
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function listQueues()
    {
        $it = new \FilesystemIterator($this->baseDirectory, \FilesystemIterator::SKIP_DOTS);

        $queues = [];

        foreach ($it as $file) {
            if (!$file->isDir()) {
                continue;
            }

            array_push($queues, $file->getBasename());
        }

        return $queues;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueue($queueName)
    {
        $queueDir = $this->getQueueDirectory($queueName);

        if (is_dir($queueDir)) {
            return;
        }

        mkdir($queueDir, 0755, true);
    }

    /**
     * {@inheritdoc}
     */
    public function countMessages($queueName)
    {
        $iterator = new \RecursiveDirectoryIterator(
            $this->getQueueDirectory($queueName),
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, '#\.job$#');

        return iterator_count($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function pushMessage($queueName, $message)
    {
        $queueDir = $this->getQueueDirectory($queueName);

        $filename = $this->getJobFilename($queueName);

        $queueSubdir = $queueDir . DIRECTORY_SEPARATOR .
            date('Y' . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'd');

        if (!is_dir($queueSubdir)) {
            mkdir($queueSubdir, 0755, true);
        }

        $filepath = $queueSubdir . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($filepath, $message);
        chmod($filepath, $this->permissions);
    }

    private function getFiles($queueName)
    {
        $queueDir = $this->getQueueDirectory($queueName);

        $files = [];

        foreach (glob($queueDir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR) as $yearDir) {
            foreach (glob($yearDir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR) as $monthDir) {
                foreach (glob($monthDir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR) as $dayDir) {
                    chdir($dayDir);
                    $files = glob("*.job", GLOB_NOSORT);

                    if ($files) {
                        $this->setCurrentQueueSubdir($dayDir);
                        break;
                    }
                }

                if ($files) {
                    break;
                }
            }

            if ($files) {
                break;
            }
        }

        natsort($files);

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function popMessage($queueName, $duration = 5)
    {
        $runtime = microtime(true) + $duration;

        $files = $this->getFiles($queueName);
        $currentQueueSubdir = $this->getCurrentQueueSubdir();

        while (microtime(true) < $runtime) {
            if ($files) {
                $id = array_shift($files);
                $data = array(file_get_contents($currentQueueSubdir.DIRECTORY_SEPARATOR.$id), $id);
                rename($currentQueueSubdir.DIRECTORY_SEPARATOR.$id, $currentQueueSubdir.DIRECTORY_SEPARATOR.$id.'.proceed');

                return $data;
            }

            usleep(1000);
        }

        return array(null, null);
    }

    /**
     * {@inheritdoc}
     */
    public function acknowledgeMessage($queueName, $receipt)
    {
        $iterator = new \RecursiveDirectoryIterator(
            $this->getQueueDirectory($queueName),
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, "#{$receipt}\.proceed$#");

        foreach ($iterator as $file) {
            /* @var $file \DirectoryIterator */
            unlink($file->getRealPath());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function peekQueue($queueName, $index = 0, $limit = 20)
    {
        $files = array_slice($this->getFiles($queueName), $index, $limit);

        $messages = [];

        foreach ($files as $file) {
            array_push(
                $messages,
                file_get_contents($this->getCurrentQueueSubdir().DIRECTORY_SEPARATOR.$file)
            );
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function removeQueue($queueName)
    {
        $iterator = new \RecursiveDirectoryIterator(
            $this->getQueueDirectory($queueName),
            \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, '#\.job$#');

        foreach ($iterator as $file) {
            /* @var $file \DirectoryIterator */
            unlink($file->getRealPath());
        }

        rmdir($this->getQueueDirectory($queueName));
    }

    /**
     * {@inheritdoc}
     */
    public function info()
    {
        return [];
    }

    /**
     * @param string $queueName
     *
     * @return string
     */
    private function getQueueDirectory($queueName)
    {
        return $this->baseDirectory.DIRECTORY_SEPARATOR.str_replace(array('\\', '.'), '-', $queueName);
    }

    /**
     * @return string
     */
    private function getCurrentQueueSubdir()
    {
        return $this->currentQueueSubdir;
    }

    /**
     * @param string $currentQueueSubdir
     */
    private function setCurrentQueueSubdir($currentQueueSubdir)
    {
        $this->currentQueueSubdir = $currentQueueSubdir;
    }

    /**
     * Generates a uuid.
     *
     * @return string
     */
    private function getJobFilename($queueName)
    {
        $path = $this->baseDirectory.'/bernard.meta';

        if (!is_file($path)) {
            touch($path);
            chmod($path, $this->permissions);
        }

        $file = new \SplFileObject($path, 'r+');
        $file->flock(LOCK_EX);

        $meta = unserialize($file->fgets());

        $id = isset($meta[$queueName]) ? $meta[$queueName] : 0;
        $id++;

        $filename = sprintf('%d.job', $id);
        $meta[$queueName] = $id;

        $content = serialize($meta);

        $file->fseek(0);
        $file->fwrite($content, strlen($content));
        $file->flock(LOCK_UN);

        return $filename;
    }
}
