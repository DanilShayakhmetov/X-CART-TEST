<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Product;

/**
 * Custom global tab model class
 *
 * @Entity
 * @Table  (name="custom_global_tabs")
 *
 * @HasLifecycleCallbacks
 */
class CustomGlobalTab extends \XLite\Model\Base\I18n
{
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
     * @OneToOne  (targetEntity="XLite\Model\Product\GlobalTab", inversedBy="custom_tab")
     * @JoinColumn (name="global_tab_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $global_tab;

    /**
     * Lifecycle callback
     *
     * @PrePersist
     */
    public function prepareBeforeSave()
    {
        $this->assignLink();
    }

    /**
     * Assign new link to tab if empty
     */
    public function assignLink()
    {
        if (
            $this->getGlobalTab()
            && !$this->getGlobalTab()->getLink()
        ) {
            $this->getGlobalTab()->setLink(
                \XLite\Core\Database::getRepo('\XLite\Model\Product\GlobalTab')->generateTabLink($this)
            );
        }
    }

    /**
     * Create entity
     *
     * @return boolean
     */
    public function create()
    {
        if (!$this->getGlobalTab()) {
            $this->setGlobalTab(new \XLite\Model\Product\GlobalTab);
            $this->getGlobalTab()->setPosition(\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->getMinPosition() - 10);
        }

        if (!$this->getGlobalTab()->isPersistent()) {
            \XLite\Core\Database::getEM()->persist($this->getGlobalTab());
            $createAliases = true;
        }

        $result = parent::create();

        if (isset($createAliases) && $result) {
            \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createGlobalTabAliases($this->getGlobalTab());
        }

        return $result;
    }

    /**
     * Return GlobalTab
     *
     * @return \XLite\Model\Product\GlobalTab
     */
    public function getGlobalTab()
    {
        return $this->global_tab;
    }

    /**
     * Set GlobalTab
     *
     * @param \XLite\Model\Product\GlobalTab $global_tab
     *
     * @return $this
     */
    public function setGlobalTab($global_tab)
    {
        $this->global_tab = $global_tab;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->getGlobalTab()->getEnabled();
    }

    /**
     * Set Enabled
     *
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->getGlobalTab()->setEnabled($enabled);
        return $this;
    }
}