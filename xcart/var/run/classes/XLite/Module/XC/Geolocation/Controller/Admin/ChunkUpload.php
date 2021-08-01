<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Controller\Admin;

use Includes\Utils\Module\Manager;

 class ChunkUpload extends \XLite\Controller\Admin\ChunkUploadAbstract implements \XLite\Base\IDecorator
{
    /**
     * Geolocation extended database upload success action
     */
    protected function geolocationSuccessAction()
    {
        $request = \XLite\Core\Request::getInstance();

        $filename = $request->filename;

        try {
            if ($path = $this->moveUploadedFile($request->basename, LC_DIR_FILES, $filename, [$this, 'geolocationFileFilter'])) {
                $oldPath = \XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path;
                if ($oldPath && file_exists($oldPath)) {
                    unlink($oldPath);
                }

                \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                    'category' => 'XC\Geolocation',
                    'name'     => 'extended_db_path',
                    'value'    => $path,
                ]);

                $redirectUrl = Manager::getRegistry()->getModuleSettingsUrl('XC', 'Geolocation');

            } else {
                $this->errors[] = 'Cannot move tmp file';

                $this->unlinkUploadedFile($request->basename);
            }
        } catch (\XLite\Core\Exception\FileValidation\AFileValidation $e) {
            $this->errors[] = 'Incorrect file extension';
            $this->unlinkUploadedFile($request->basename);
        }

        $params = [
            'status'  => $this->getStatus(),
            'message' => implode('. ', $this->errors),
        ];

        if (isset($redirectUrl)) {
            $params['redirectUrl'] = $redirectUrl;
        }

        echo json_encode($params);
    }

    /**
     * Filter file uploaded by geolocation
     *
     * @param $tmpPath string current file location(can be used to read file content)
     * @param $newName string desired file name
     *
     * @throws \XLite\Core\Exception\FileValidation\AFileValidation
     */
    protected function geolocationFileFilter($tmpPath, $newName)
    {
        if (substr($newName, -5) !== '.mmdb') {
            throw new \XLite\Core\Exception\FileValidation\IncorrectName;
        }
    }
}