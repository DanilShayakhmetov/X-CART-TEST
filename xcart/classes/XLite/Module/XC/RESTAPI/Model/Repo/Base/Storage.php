<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model\Repo\Base;

/**
 * Storage  repository
 */
abstract class Storage extends \XLite\Model\Repo\Base\Storage implements \XLite\Base\IDecorator
{

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(\XLite\Model\AEntity $entity, array $record, array $regular = array(), array $assocs = array())
    {
        $path = null;
        if (!empty($record['content']) && !empty($record['path'])) {
            $path = $record['path'];
            unset($record['path']);
        }

        parent::loadRawFixture($entity, $record, $regular, $assocs);

        if (\XLite\Core\Database::getEM()->contains($entity) && !empty($record['content'])) {
            $content = base64_decode($record['content'], true);
            if ($content) {
                $filename = basename($path);
                $path = LC_DIR_TMP . $filename;
                file_put_contents($path, $content);
                unset($content);
                $entity->loadFromLocalFile($path, $filename);
                unlink($path);
            }
        }
    }

}
