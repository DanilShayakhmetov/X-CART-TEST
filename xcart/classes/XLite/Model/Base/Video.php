<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Image abstract store
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Video extends \XLite\Model\Base\Storage
{
    /**
     * MIME type to extension translation table
     *
     * @var array
     */
    protected static $types = [
        'video/mpeg'      => 'mpeg',
        'video/mp4'       => 'mp4',
        'video/ogg'       => 'ogv',
        'video/quicktime' => 'mov',
        'video/webm'      => 'webm',
        'video/x-ms-wmv'  => 'wmv',
        'video/x-msvideo' => 'avi',
        'video/x-flv'     => 'flv',
        'video/3gpp'      => '3gp',
        'video/3gpp2'     => '3g2',
    ];

    /**
     * Check file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return false;
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getValidFileSystemRoot()
    {
        $path = parent::getValidFileSystemRoot();

        if (!file_exists($path . LC_DS . '.htaccess')) {
            $contents = <<<HTACCESS
Options -Indexes

<Files "*.php">
  Deny from all
</Files>

<Files "*.php3">
  Deny from all
</Files>

<Files "*.pl">
  Deny from all
</Files>

<Files "*.py">
  Deny from all
</Files>

Allow from all
HTACCESS;

            file_put_contents(
                $path . LC_DS . '.htaccess',
                $contents
            );
        }

        return $path;
    }

}
