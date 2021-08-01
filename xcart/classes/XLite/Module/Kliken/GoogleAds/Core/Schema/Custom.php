<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema;

/**
 * Custom schema
 */
class Custom extends \XLite\Module\XC\RESTAPI\Core\Schema\Native
{
    /**
     * Schema code
     */
    const CODE = 'kliken';

    /**
     * Constructor
     *
     * @param \XLite\Core\Request $request Request
     * @param string              $method  Method
     *
     * @return void
     */
    public function __construct(\XLite\Core\Request $request, $method)
    {
        parent::__construct($request, $method);

        $this->modelSchema = $this->buildModelSchema();
    }

    /**
     * Build schema for concrete model
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
     * @throws \Exception
     */
    protected function buildModelSchema()
    {
        $classes = $this->getAllowedEntityClasses();
        $className = $this->config->class && isset($classes[$this->config->class])
            ? $classes[$this->config->class]
            : null;

        if (!class_exists($className)) {
            throw new \Exception(
                sprintf("There is no model schema for that class: %s", $this->config->class)
            );
        }

        return new $className();
    }

    /**
     * Check - entity class is allowed or not
     *
     * @param string $class Entity class name
     *
     * @return boolean
     */
    protected function isAllowedEntityClass($class)
    {
        return parent::isAllowedEntityClass($class)
            && array_key_exists($class, $this->getAllowedEntityClasses());
    }

    /**
     * Get allowed entity classes
     *
     * @return array
     */
    protected function getAllowedEntityClasses()
    {
        return [
            'XLite\Model\Product'                  => 'XLite\Module\Kliken\GoogleAds\Core\Schema\Complex\Product',
            'XLite\Model\Category'                 => 'XLite\Module\Kliken\GoogleAds\Core\Schema\Complex\Category',
            'XLite\Model\Order'                    => 'XLite\Module\Kliken\GoogleAds\Core\Schema\Complex\Order',
            'XLite\Module\CDev\SalesTax\Model\Tax' => 'XLite\Module\Kliken\GoogleAds\Core\Schema\Complex\CDevTax',
            'XLite\Model\Shipping\Markup'          => 'XLite\Module\Kliken\GoogleAds\Core\Schema\Complex\ShippingMarkup',
        ];
    }

    /**
     * Convert model
     *
     * @param mixed   $model            Model OPTIONAL
     * @param boolean $withAssociations Convert with associations OPTIONAL
     *
     * @return mixed
     */
    protected function convertModel($model = null, $withAssociations = true)
    {
        return $model
            ? $this->modelSchema->convertModel($model, $withAssociations)
            : null;
    }
}
