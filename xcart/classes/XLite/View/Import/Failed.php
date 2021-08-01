<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Failed section
 */
class Failed extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/failed.twig';
    }

    /**
     * Get default header for page
     *
     * @return string
     */
    protected function getDefaultFailedHeader()
    {
        return static::t('Verification results');
    }

    /**
     * Get header for page
     *
     * @return string
     */
    protected function getFailedHeader()
    {
        $result = $this->getDefaultFailedHeader();

        if ($this->isCanceled()) {
            $result = static::t('The import process has been canceled');

        } elseif (\XLite\Core\TmpVars::getInstance()->lastImportStep === 'XLite\Logic\Import\Step\Import') {
            $result = static::t('Import results');
        }

        return $result;
    }

    /**
     * Check if import was canceled
     *
     * @return boolean
     */
    protected function isCanceled()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');

        return $repo->getVar(\XLite\Logic\Import\Importer::getImportCancelFlagVarName());
    }

    /**
     * Return true if import process has errors
     *
     * @return boolean
     */
    protected function hasErrors()
    {
        return \XLite\Logic\Import\Importer::hasErrors();
    }

    /**
     * Return true if import process has errors
     *
     * @return boolean
     */
    protected function hasErrorsOrWarnings()
    {
        return \XLite\Logic\Import\Importer::hasErrors() || \XLite\Logic\Import\Importer::hasWarnings();
    }

    /**
     * Return true if import process has been broken by user
     *
     * @return boolean
     */
    protected function isBroken()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');

        return $repo->getVar(\XLite\Logic\Import\Importer::getImportUserBreakFlagVarName());
    }

    /**
     * Return true if 'Proceed' button should be displayed
     *
     * @return boolean
     */
    protected function isDisplayProceedButton()
    {
        return !$this->hasErrors() && !$this->isBroken();
    }

    /**
     * Return files list
     *
     * @return array
     */
    protected function getFiles()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->findFiles();
    }

    /**
     * Return errors list
     *
     * @param string $file File
     *
     * @return array
     */
    protected function getErrors($file)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->findErrorsByFile($file);
    }

    /**
     * Return errors list grouped
     *
     * @param string $file File
     *
     * @return array
     */
    protected function getErrorsGroups($file)
    {
        $errors = $this->getErrors($file);
        $result = [];
        array_walk(
            $errors,
            function ($error) use (&$result) {
                $uniqueHash = md5($error['code'] . serialize($error['arguments']));
                if (!array_key_exists($uniqueHash, $result)) {
                    $result[$uniqueHash] = [
                        'code'      => $error['code'],
                        'type'      => $error['type'],
                        'arguments' => $error['arguments'],
                        'errors'    => [],
                    ];
                }
                $result[$uniqueHash]['errors'][] = $error;
            }
        );

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        return static::t(
            'The script found {{number}} errors during verification',
            [
                'number' => \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->count(),
            ]
        );
    }

    /**
     * Return error message
     *
     * @param array $error Error
     *
     * @return string
     */
    protected function getErrorMessage($error)
    {
        $messages = $this->getErrorMessages();

        $result = static::t(
            'Row {{number}}',
            ['number' => $error['row']]
        );

        $result .= ': ';

        $result .= isset($messages[$error['code']])
            ? static::t($messages[$error['code']], is_array($error['arguments']) ? $error['arguments'] : [])
            : $error['code'];

        return $result;
    }

    /**
     * Return group error message
     *
     * @param array $errorGroup
     *
     * @return string
     */
    protected function getGroupErrorMessage($errorGroup)
    {
        $messages = $this->getErrorMessages();

        $result = isset($messages[$errorGroup['code']])
            ? static::t($messages[$errorGroup['code']], is_array($errorGroup['arguments']) ? $errorGroup['arguments'] : [])
            : $errorGroup['code'];

        return $result;
    }

    /**
     * Return group error rows
     *
     * @param array $errorGroup
     *
     * @return string
     */
    protected function getGroupErrorRows($errorGroup)
    {
        $rows = array_map(function ($error) {
            return $error['row'];
        }, $errorGroup['errors']);

        $rows = array_unique($rows);

        return 1 < count($rows)
            ? static::t(
                'Row(s) {{numbers}}',
                ['numbers' => implode(', ', array_unique($rows))]
            )
            : static::t(
                'Row {{number}}',
                ['number' => array_pop($rows)]
            );
    }

    /**
     * Return error text
     *
     * @param array $error Error
     *
     * @return string
     */
    protected function getErrorText($error)
    {
        $texts = $this->getErrorTexts();

        return isset($texts[$error['code']])
            ? static::t($texts[$error['code']])
            : ('E' === $error['type'] ? static::t('Critical error') : static::t('Warning'));
    }

    /**
     * Return error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $result = [];

        foreach (\XLite\Logic\Import\Importer::getProcessorList() as $processor) {
            $result = array_merge($result, $processor::getMessages());
        }

        return $result;
    }

    public function getLinesMessage()
    {
        $result = '';

        /** @var \XLite\Logic\Import\Importer $importer */
        $importer  = $this->getImporter() ?: $this->getCancelledImporter();
        $lastClass = \XLite\Core\TmpVars::getInstance()->lastImportStep;

        if ($importer && $lastClass) {
            foreach ($importer->getSteps() as $step) {
                if ($step instanceof $lastClass) {
                    $result = $step->getNormalLanguageLabel();
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Return error texts
     *
     * @return array
     */
    protected function getErrorTexts()
    {
        $result = [];
        foreach (\XLite\Logic\Import\Importer::getProcessorList() as $processor) {
            $result = array_merge($result, $processor::getErrorTexts());
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getManualLinks()
    {
        $result = [];

        $files = $this->getFiles();
        foreach ($files as $k => $file) {
            if (isset($file['processor'])
                && \Includes\Utils\Operator::checkIfClassExists($file['processor'])
            ) {
                $manualURL = call_user_func([$file['processor'], 'getCSVFormatManualURL']);

                if ($manualURL) {
                    $result[] = array_merge($file, ['manualURL' => $manualURL]);
                }
            }
        }

        return $result;
    }
}
