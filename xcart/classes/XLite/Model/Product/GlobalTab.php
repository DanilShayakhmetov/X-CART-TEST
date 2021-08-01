<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Product;

/**
 * The "tab" model class
 *
 * @Entity
 * @Table  (name="global_product_tabs",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="service_name", columns={"service_name"})
 *      }
 * )
 */
class GlobalTab extends \XLite\Model\AEntity implements \XLite\Model\Product\IProductTab
{
    const TYPE_WIDGET = 'widget';
    const TYPE_LIST = 'list';
    const TYPE_TEMPLATE = 'template';

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->providers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

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
     * Tab position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Tab name
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $service_name;

    /**
     * Tab provider(module namespace or core)
     *
     * @var \XLite\Model\Product\GlobalTabProvider[]
     *
     * @OneToMany (targetEntity="XLite\Model\Product\GlobalTabProvider", mappedBy="tab", cascade={"all"})
     */
    protected $providers;

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
     * Return Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set Position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Return Name
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set Name
     *
     * @param string $service_name
     *
     * @return $this
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;
        return $this;
    }

    /**
     * Return Providers
     *
     * @return GlobalTabProvider[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Set Providers
     *
     * @param GlobalTabProvider $provider
     *
     * @return $this
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Get provider by code
     *
     * @param $code
     *
     * @return array
     */
    public function getProviderByCode($code)
    {
        $provider = array_filter($this->getProviders()->toArray(), function ($v, $k) use ($code) {
            /** @var \XLite\Model\Product\GlobalTabProvider $v */
            return $v->getCode() === $code;
        }, ARRAY_FILTER_USE_BOTH);

        return !empty($provider) ? array_shift($provider) : null;
    }

    /**
     * Check if at least one provider available
     *
     * @return boolean
     */
    public function checkProviders()
    {
        foreach ($this->getProviders() as $provider) {
            if ($provider->checkProvider()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if tab available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->checkProviders();
    }

    /**
     * Returns tab name
     *
     * @return string
     */
    public function getName()
    {
        return static::t($this->getServiceName());
    }
}