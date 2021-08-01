<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Admin;

/**
 * Export
 */
 class Export extends \XLite\Controller\Admin\ExportAbstract implements \XLite\Base\IDecorator
{
    const NFO_EXPORT_TMP_NAME = 'nfo_export';

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionItemlistExport()
    {
        $options = $this->assembleItemsListExportOptions();

        \XLite\Core\TmpVars::getInstance()->{static::NFO_EXPORT_TMP_NAME} = [];

        if (!empty($options['include'])) {
            foreach ($options['include'] as $include) {
                if ('XLite\Logic\Export\Step\Orders' === $include) {
                    $orderRepo = \XLite\Core\Database::getRepo('\XLite\Model\Order');
                    $orderRepo->setExportSelection($options['selection']);

                    \XLite\Core\TmpVars::getInstance()->{static::NFO_EXPORT_TMP_NAME} = [
                        'ordersCount' => $orderRepo->countForExport(),
                        'nfoCount' => $orderRepo->countNFOForExport(),
                    ];
                }
            }
        }

        if (!$this->isExportHasOnlyNFO()) {
            parent::doActionItemlistExport();
        }

        $this->setPureAction(true);
    }

    /**
     * Check - export process has only NF orders
     *
     * @return boolean
     */
    public function isExportHasOnlyNFO()
    {
        $nfoExportData = \XLite\Core\TmpVars::getInstance()->{static::NFO_EXPORT_TMP_NAME};

        return !empty($nfoExportData)
            && !empty($nfoExportData['nfoCount'])
            && empty($nfoExportData['ordersCount']);
    }

    /**
     * Check - export process has only NF orders
     *
     * @return boolean
     */
    public function isExportHasNFO()
    {
        $nfoExportData = \XLite\Core\TmpVars::getInstance()->{static::NFO_EXPORT_TMP_NAME};

        return !empty($nfoExportData)
            && !empty($nfoExportData['nfoCount']);
    }
}