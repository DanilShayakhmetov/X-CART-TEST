<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for backup page.
 */
class Backup extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Widget parameter names
     */
    const PARAM_IS_FILE_WRITABLE = 'isFileWritable';
    const PARAM_IS_FILE_EXISTS = 'isFileExists';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_IS_FILE_WRITABLE => new \XLite\Model\WidgetParam\TypeBool('is file writable'),
            self::PARAM_IS_FILE_EXISTS   => new \XLite\Model\WidgetParam\TypeBool('is file exists'),
        );
    }
    
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $isFileWritable = $this->getParam(self::PARAM_IS_FILE_WRITABLE);
        $isFileExists = $this->getParam(self::PARAM_IS_FILE_EXISTS);
        
        $list = [];
        $list['begin_import'] = $this->getWidget(
            [
                'label'    => static::t('Download SQL file'),
                'style' => 'regular-main-button download-sql always-enabled',
            ],
            '\XLite\View\Button\Submit'
        );

        if ($isFileWritable) {
            if ($isFileExists) {
                $list['delete_sql_file'] = $this->getWidget(
                    [
                        'label'    => static::t('Delete SQL file'),
                        'style' => 'delete-sql-file',
                        'action' => 'delete'
                    ],
                    '\XLite\View\Button\Regular'
                );
            }

            $list['create_sql_file'] = $this->getWidget(
                [
                    'label'    => static::t('Create SQL file'),
                    'style' => 'create-sql-file',
                    'action' => 'backup_write_to_file'
                ],
                '\XLite\View\Button\Regular'
            );

            $list['sql-file-help'] = $this->getWidget(
                [
                    'id' => 'create-sql-file-help',
                    'text'    => static::t('If you choose to create SQL file, you will be able to download the file from the server later and after that delete it from the server by clicking on the "Delete SQL file" button.'),
                    'isImageTag' => true,
                    'className' => 'help-icon'
                ],
                '\XLite\View\Tooltip'
            );
        }

        return $list;
    }

    /**
     * Check - sticky panel is active only if form is changed
     *
     * @return boolean
     */
    protected function isFormChangeActivation()
    {
        return false;
    }
}
