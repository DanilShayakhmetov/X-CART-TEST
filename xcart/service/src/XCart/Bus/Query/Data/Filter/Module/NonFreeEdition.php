<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use ArrayIterator;
use Iterator;
use XCart\Bus\Client\LicenseClient;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

class NonFreeEdition extends AFilter
{
    /**
     * @var array
     */
    private $freeLicenseInfo;

    /**
     * @param Iterator      $iterator
     * @param string        $field
     * @param mixed         $data
     * @param LicenseClient $licenseClient
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        LicenseClient $licenseClient
    ) {
        $titerator = new \AppendIterator();
        $titerator->append($iterator);
        $titerator->append($this->getFakeModulesIterator());

        parent::__construct($titerator, $field, $data);

        $this->freeLicenseInfo = $licenseClient->getFreeLicenseInfo();
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $freeEditionName = $this->freeLicenseInfo['editionName'];

        return !empty($item->editions) && !in_array($freeEditionName, $item->editions, true);
    }

    /**
     * @return ArrayIterator
     */
    private function getFakeModulesIterator(): ArrayIterator
    {
        $aom = $this->getAOMModule();
        $paymentTransactions = $this->getPaymentTransactionsModule();

        return new ArrayIterator([
            $aom->id => $aom,
            $paymentTransactions->id => $paymentTransactions
        ]);
    }

    /**
     * @return Module
     */
    private function getAOMModule(): Module
    {
        return new Module([
            'id' => 'XC-AOM',

            'version'          => '0.0.0.0',
            'installedVersion' => '0.0.0.0',

            'type' => 'fake',

            'author'      => 'XC',
            'name'        => 'AOM',
            'authorName'  => 'X-Cart team',
            'moduleName'  => 'Advanced Order Management',
            'description' => '',

            'minorRequiredCoreVersion' => 0,

            'dependsOn'        => [],
            'incompatibleWith' => [],
            'requiredBy'       => [],

            'showSettingsForm' => false,

            'isSystem'   => false,
            'canDisable' => true,

            'icon' => 'skins/admin/images/icon_aom.png',

            'installed'     => true,
            'installedDate' => 0,
            'enabled'       => true,
            'enabledDate'   => 0,
            'skinPreview'   => '',

            'pageUrl'       => null,
            'authorPageUrl' => null,
            'authorEmail'   => null,

            'revisionDate' => 0,
            'price'        => 0.0,
            'downloads'    => 0,
            'rating'       => 0,
            'tags'         => [],
            'translations' => [],

            'wave'         => null,
            'editions'     => ['Business'],
            'editionState' => null,

            'actions'       => [],
            'scenarioState' => [
                'enabled'   => false,
                'installed' => false,
            ],

            'private'         => false,
            'xbProductId'     => null,
            'custom'          => false,
            'purchaseUrl'     => '',
            'salesChannelPos' => -1,
            'isLanding'       => false,
            'hash'            => [],
        ]);
    }

    /**
     * @return Module
     */
    private function getPaymentTransactionsModule(): Module
    {
        return new Module([
            'id' => 'XC-PT',

            'version'          => '0.0.0.0',
            'installedVersion' => '0.0.0.0',

            'type' => 'fake',

            'author'      => 'XC',
            'name'        => 'PT',
            'authorName'  => 'X-Cart team',
            'moduleName'  => 'Payment transactions',
            'description' => '',

            'minorRequiredCoreVersion' => 0,

            'dependsOn'        => [],
            'incompatibleWith' => [],
            'requiredBy'       => [],

            'showSettingsForm' => false,

            'isSystem'   => false,
            'canDisable' => true,

            'icon' => 'skins/admin/images/icon_pt.png',

            'installed'     => true,
            'installedDate' => 0,
            'enabled'       => true,
            'enabledDate'   => 0,
            'skinPreview'   => '',

            'pageUrl'       => null,
            'authorPageUrl' => null,
            'authorEmail'   => null,

            'revisionDate' => 0,
            'price'        => 0.0,
            'downloads'    => 0,
            'rating'       => 0,
            'tags'         => [],
            'translations' => [],

            'wave'         => null,
            'editions'     => ['Business'],
            'editionState' => null,

            'actions'       => [],
            'scenarioState' => [
                'enabled'   => false,
                'installed' => false,
            ],

            'private'         => false,
            'xbProductId'     => null,
            'custom'          => false,
            'purchaseUrl'     => '',
            'salesChannelPos' => -1,
            'isLanding'       => false,
            'hash'            => [],
        ]);
    }
}
