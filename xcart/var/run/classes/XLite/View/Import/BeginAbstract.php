<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Begin section
 */
abstract class BeginAbstract extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    const MODE_UPDATE_AND_CREATE = 'UC';
    const MODE_UPDATE_ONLY       = 'U';
    const MODE_CREATE_ONLY       = 'C';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/begin.twig';
    }

    /**
     * Defines the message for uploading files
     *
     * @return string
     */
    protected function getUploadFileMessage()
    {
        return static::t(
            'CSV or ZIP files, total max size: {{size}}',
            ['size' => $this->getReadableUploadFileMaxSize()]
        );
    }

    /**
     * @return int
     */
    protected function getUploadFileMaxSize()
    {
        return \XLite\Core\Converter::convertShortSize(
            \XLite\Core\Converter::getUploadFileMaxSize()
        );
    }

    /**
     * @return int
     */
    protected function getReadableUploadFileMaxSize()
    {
        return \XLite\Core\Converter::convertShortSizeToHumanReadable(
            $this->getUploadFileMaxSize()
        );
    }

    /**
     * Return samples URL
     *
     * @return string
     */
    protected function getSamplesURL()
    {
        return static::t('https://kb.x-cart.com/import-export/');
    }

    /**
     * Return samples URL text
     *
     * @return string
     */
    protected function getSamplesURLText()
    {
        return static::t('Import/Export guide');
    }

    /**
     * Check - charset enabledor not
     *
     * @return boolean
     */
    protected function isCharsetEnabled()
    {
        return \XLite\Core\Iconv::getInstance()->isValid();
    }

    /**
     * Return comment text for 'updateOnly' checkbox tooltip
     *
     * @return string
     */
    protected function getImportModeComment()
    {
        $result = '';

        $importer = $this->getImporter() ?: null;

        if (!$importer) {
            $importer = new \XLite\Logic\Import\Importer([]);
        }

        $keys = $importer->getAvailableEntityKeys();

        if ($keys) {
            $rows = [];
            foreach ($keys as $key => $list) {
                $rows[] = '<li>' . $key . ': <em>' . implode(', ', $list) . '</em></li>';
            }
            $result = static::t('Import mode comment', ['keys' => implode('', $rows)]);
        }

        return $result;
    }

    /**
     * Get options for selector 'Import mode'
     *
     * @return array
     */
    protected function getImportModeOptions()
    {
        return [
            static::MODE_UPDATE_AND_CREATE => static::t('Create new items and update existing items'),
            static::MODE_UPDATE_ONLY       => static::t('Update existing items, but skip new items'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'File size exceeds the maximum size' => static::t('File size exceeds the maximum size')
        ];
    }
}
