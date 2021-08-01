<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Product;

use Includes\Utils\Module\Manager;

/**
 * The "tab" model class
 *
 * @Entity
 * @Table  (name="global_product_tab_provider")
 */
class GlobalTabProvider extends \XLite\Model\AEntity
{
    const PROVIDER_CORE = 'Core';

    /**
     * Tab unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Global tab
     *
     * @var \XLite\Model\Product\GlobalTab
     * @ManyToOne  (targetEntity="XLite\Model\Product\GlobalTab", inversedBy="custom_tab")
     * @JoinColumn (name="tab_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tab;

    /**
     * Tab name
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $code;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Tab
     *
     * @return GlobalTab
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * Set Tab
     *
     * @param GlobalTab $tab
     *
     * @return $this
     */
    public function setTab($tab)
    {
        $this->tab = $tab;
        return $this;
    }

    /**
     * Return Provider
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Provider
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Check if tab provider available
     *
     * @return bool
     */
    public function checkProvider()
    {
        return $this->getCode() === static::PROVIDER_CORE
               || Manager::getRegistry()->isModuleEnabled($this->getCode());
    }
}