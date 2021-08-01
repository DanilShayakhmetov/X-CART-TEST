<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\TopMessage;
use XLite\Core\Database;
use XLite\Logic\ApplyAttributeValues\Generator as ApplyAttrValuesGenerator;

/**
 * Product
 */
class Product extends \XLite\Controller\Admin\ACL\Catalog
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * Backward compatibility
     *
     * @var array
     */
    protected $params = array('target', 'id', 'product_id', 'page', 'backURL');

    /**
     * Chuck length
     */
    const CHUNK_LENGTH = 100;

    // {{{ Abstract method implementations

    /**
     * Check if we need to create new product or modify an existing one
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return boolean
     */
    public function isNew()
    {
        return !$this->getProduct()->isPersistent();
    }

    /**
     * Defines the product preview URL
     *
     * @param integer $productId Product id
     *
     * @return string
     */
    public function buildProductPreviewURL($productId)
    {
        return \XLite\Core\Converter::buildURL(
            'product',
            'preview',
            [
                'product_id' => $productId,
                'shopKey'    => \XLite\Core\Auth::getInstance()->getShopKey(),
            ],
            \XLite::getCustomerScript()
        );
    }

    /**
     * Return model form object
     *
     * @param array $params Form constructor params OPTIONAL
     *
     * @return \XLite\View\Model\AModel|void
     */
    public function getInventoryModelForm(array $params = array())
    {
        $class = '\XLite\View\Model\InventoryTracking';

        return \XLite\Model\CachingFactory::getObject(
            __METHOD__ . $class . (empty($params) ? '' : md5(serialize($params))),
            $class,
            $params
        );
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    protected function getEntity()
    {
        return $this->getProduct();
    }

    // }}}

    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['info'] = static::t('Info');

        if (!$this->isNew()) {
            $list['attributes'] = [
                'title' => static::t('Attributes'),
                'subTabsWidget' => '\XLite\View\Tabs\Attributes',
                'subTabsWidgetParams' => [
                    'product' => $this->getProduct()
                ],
            ];
            $list['inventory']  = static::t('Inventory tracking');
        }

        return $list;
    }

    /**
     * Get spages sections
     *
     * @return array
     */
    public function getSPages()
    {
        $list['global'] = static::t('Global');
        $list['hidden'] = static::t('Hidden');
        $list['custom'] = static::t('Custom');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['info']    = 'product/info.twig';
        $list['default'] = 'product/info.twig';

        if (!$this->isNew()) {
            $list['attributes'] = 'product/attributes.twig';
            $list['inventory']  = 'product/inventory.twig';
        }

        return $list;
    }

    // }}}

    // {{{ Data management

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        $result = $this->productCache
            ?: Database::getRepo('\XLite\Model\Product')->find($this->getProductId());

        if (null === $result) {
            $result = new \XLite\Model\Product();
            
            if (
                \XLite\Core\Request::getInstance()->category_id > 1 && 
                ($category = Database::getRepo('XLite\Model\Category')->find(\XLite\Core\Request::getInstance()->category_id))
            ) {
                $result->addCategory($category);
            }
        }

        return $result;
    }

    /**
     * Returns the categories of the product
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->isNew()
            ? array(
                $this->getCategoryId(),
            )
            : $this->getProduct()->getCategories();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProduct() && $this->getProduct()->isPersistent()
            ? $this->getProduct()->getName()
            : static::t('Add product');
    }

    /**
     * Get product category id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        $categoryId = parent::getCategoryId();

        if (empty($categoryId) && !$this->isNew()) {
            $categoryId = $this->getProduct()->getCategoryId();
        }

        return $categoryId;
    }

    /**
     * Return current product Id
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return integer
     */
    public function getProductId()
    {
        $result = $this->productCache
            ? $this->productCache->getProductId()
            : (int) \XLite\Core\Request::getInstance()->product_id;

        if (0 >= $result) {
            $result = (int) \XLite\Core\Request::getInstance()->id;
        }

        return $result;
    }

    /**
     * The product can be set from the view classes
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function setProduct(\XLite\Model\Product $product)
    {
        $this->productCache = $product;
    }

    /**
     * Get posted data
     *
     * @param string $field Name of the field to retrieve OPTIONAL
     *
     * @return mixed
     */
    protected function getPostedData($field = null)
    {
        $value = parent::getPostedData($field);

        $time = \XLite\Core\Converter::time();

        if (null === $field) {
            if (isset($value['arrivalDate'])) {
                $value['arrivalDate'] = ((int) strtotime($value['arrivalDate']))
                    ?: mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time));
            }

            if (isset($value['sku']) && \XLite\Core\Converter::isEmptyString($value['sku'])) {
                $value['sku'] = null;
            }

            if (isset($value['productClass'])) {
                $value['productClass'] = Database::getRepo('\XLite\Model\ProductClass')
                    ->find($value['productClass']);
            }

            if (isset($value['taxClass'])) {
                $value['taxClass'] = Database::getRepo('\XLite\Model\TaxClass')->find($value['taxClass']);
            }

        } elseif ('arrivalDate' === $field) {
            $value = ((int) strtotime($value)) ?: mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time));

        } elseif ('sku' === $field) {
            $value = null;

        } elseif ('productClass' === $field) {
            $value = Database::getRepo('\XLite\Model\ProductClass')->find($value);

        } elseif ('taxClass' === $field) {
            $value = Database::getRepo('\XLite\Model\TaxClass')->find($value);
        }

        return $value;
    }

    // }}}

    // {{{ Action handlers

    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $product = $this->getProduct();
        $isPersistent = $product->isPersistent();

        $formModel = new \XLite\View\FormModel\Product\Info(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($product, $rawData[$this->formName]);
            Database::getEM()->persist($product);
            Database::getEM()->flush();

            $dto->afterPopulate($product, $rawData[$this->formName]);
            if (!$isPersistent) {
                $dto->afterCreate($product, $rawData[$this->formName]);
                TopMessage::addInfo('Product has been created');

            } else {
                $dto->afterUpdate($product, $rawData[$this->formName]);
                TopMessage::addInfo('Product has been updated');
            }
            Database::getEM()->flush();

        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);

            foreach ($form->getErrors(true) as $error) {
                TopMessage::addError($error->getMessage());
            }
        }

        $productId = $product->getProductId() ?: $this->getProductId();

        $params = $productId ? ['product_id' => $productId] : [];

        $this->setReturnURL($this->buildURL('product', '', $params));
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        return new \XLite\Model\DTO\Product\Info($this->getProduct());
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    // protected function doActionUpdate()
    // {
    //     $this->getModelForm()->performAction('modify');
    //
    //     $this->setReturnURL(
    //         $this->buildURL(
    //             'product',
    //             '',
    //             array(
    //                 'product_id' => $this->getProductId()
    //             )
    //         )
    //     );
    // }

    // TODO: refactor

    /**
     * Do action clone
     *
     * @return void
     */
    protected function doActionClone()
    {
        if ($this->getProduct()) {
            $newProduct = $this->getProduct()->cloneEntity();
            $newProduct->updateQuickData();
            $this->setReturnURL($this->buildURL('product', '', array('product_id' => $newProduct->getId())));
        }
    }

    /**
     * Update inventory
     *
     * @return void
     */
    protected function doActionUpdateInventory()
    {
        $dto = $this->getInventoryFormModelObject();

        $formModel = new \XLite\View\FormModel\Product\Inventory(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($this->getProduct());
            Database::getEM()->flush();
        } else {
            \XLite\Core\Session::getInstance()->{$this->formModelDataSessionCellName} = $data[$this->formName];

            foreach ($form->getErrors(true) as $error) {
                TopMessage::addError($error->getMessage());
            }
        }

        $params = ['page' => 'inventory'];
        $params = $this->getProductId() ? array_replace($params, ['product_id' => $this->getProductId()]) : $params;

        $this->setReturnURL($this->buildURL('product', '', $params));
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getInventoryFormModelObject()
    {
        return new \XLite\Model\DTO\Product\Inventory($this->getProduct());
    }


    /**
     * Update attributes
     *
     * @return void
     */
    protected function doActionUpdateAttributes()
    {
        Database::getEM()->clear();
        $request = \XLite\Core\Request::getInstance();
        $name           = $request->name;
        $attributeValue = $request->attributeValue;
        $delete         = $request->delete;
        $newValue       = $request->newValue;
        $saveGlobally   = $request->save_mode;
        $displayMode    = $request->displayMode;

        // Initialize non-filtered request data
        $nonFilteredData = $request->getNonFilteredData();

        $repo      = Database::getRepo(\XLite\Model\Attribute::class);
        $repoGroup = Database::getRepo(\XLite\Model\AttributeGroup::class);

        $product = $this->getProduct();

        if ($saveGlobally) {
            $oldValues = $this->getAttributeValues();
        }

        if ($delete) {
            foreach ($delete as $k => $v) {
                if (isset($name[$k])) {
                    unset($name[$k]);
                }
                if (isset($attributeValue[$k])) {
                    unset($attributeValue[$k]);
                }
                /** @var \XLite\Model\Attribute $a */
                $a = $repo->find($k);
                if ($a) {
                    $this->removeAttributeEntity($a);
                }
            }
        }

        $maxPosition = 0;
        if ($name) {
            /** @var \XLite\Model\Attribute[] $attributes */
            $attributes = $repo->findByIds(array_keys($name));

            if ($attributes) {
                foreach ($attributes as $a) {
                    $id = $a->getId();
                    if ($name[$id]) {
                        $a->setName($name[$id]);
                        $maxPosition = max($maxPosition, $a->getPosition($product));
                    }
                    if (isset($displayMode[$id])) {
                        $prop = $a->getProperty($product);
                        $prop->setDisplayMode($displayMode[$id]);

                        if ($a->getProduct()) {
                            $a->setDisplayMode($displayMode[$id]);
                        }
                    }
                }
            }
        }

        if ($attributeValue) {
            $attributes = $repo->findByIds(array_keys($attributeValue));

            if ($attributes) {

                $attributeValueNonFiltered = !empty($nonFilteredData['attributeValue'])
                    ? $nonFilteredData['attributeValue']
                    : $attributeValue;

                foreach ($attributes as $a) {
                    if (isset($attributeValue[$a->getId()])) {
                        $value = $this->isAttributeValueAllowsTags($a)
                            ? $this->purifyValue($attributeValueNonFiltered[$a->getId()])
                            : $attributeValue[$a->getId()];
                        $a->setAttributeValue($product, $value);
                    }
                }
            }
        }

        if ($newValue) {

            $newValueNonFiltered = !empty($nonFilteredData['newValue'])
                ? $nonFilteredData['newValue']
                : $newValue;

            foreach ($newValue as $k => $data) {
                $data['name'] = trim($data['name']);
                if (
                    $data['name']
                    && $data['type']
                    && \XLite\Model\Attribute::getTypes($data['type'])
                ) {
                    $a = new \XLite\Model\Attribute();
                    $a->setName($data['name']);
                    $a->setType($data['type']);
                    $a->setPosition([
                        'product' => $product,
                        'position' => ++$maxPosition
                    ]);
                    if ($data['type'] === \XLite\Model\Attribute::TYPE_SELECT) {
                        $a->setDisplayMode($data['displayMode'], true);
                    }

                    if (0 < $data['listId']) {
                        $group = $repoGroup->find($data['listId']);
                        if ($group) {
                            $a->setAttributeGroup($group);
                            $a->setProductClass($group->getProductClass());
                        }

                    } elseif (
                        -2 == $data['listId']
                        && $product->getProductClass()
                    ) {
                        $a->setProductClass($product->getProductClass());

                    } elseif (-3 == $data['listId']) {
                        $a->setProduct($product);
                        $product->addAttributes($a);
                    }

                    unset($data['name'], $data['type'], $data['displayMode']);
                    $repo->insert($a);

                    if ($this->isAttributeValueAllowsTags($a)) {
                        $data = $this->purifyValue($newValueNonFiltered[$k]);
                    }

                    $a->setAttributeValue($product, $data);
                }
            }
        }

        $product->updateQuickData();

        if ($saveGlobally) {
            $this->applyAttributeValuesChanges(
                $oldValues,
                $this->getAttributeValues()
            );
        }

        Database::getEM()->flush();
        if (!$saveGlobally) {
            \XLite\Core\TopMessage::addInfo('Attributes have been updated successfully');
        }
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     */
    protected function removeAttributeEntity(\XLite\Model\Attribute $attribute)
    {
        $repo = Database::getRepo(\XLite\Model\Attribute::class);

        $repo->delete($attribute, false);
    }

    /**
     * Return true if attribute allows tags in its values
     *
     * @param \XLite\Model\Attribute $attr Attribute
     *
     * @return boolean
     */
    protected function isAttributeValueAllowsTags(\XLite\Model\Attribute $attr)
    {
        return $attr->getType() == \XLite\Model\Attribute::TYPE_TEXT;
    }

    /**
     * Purify an attribute value
     *
     * @param array $value
     *
     * @return string
     */
    protected function purifyValue($value)
    {
        $value['value'] = \XLite\Core\HTMLPurifier::purify($value['value']);

        return $value;
    }

    /**
     * Update attributes properties
     *
     * @return void
     */
    protected function doActionUpdateAttributesProperties()
    {
        $list = new \XLite\View\ItemsList\AttributeProperty;
        $list->processQuick();
    }

    /**
     * Get attribute values for diff
     *
     * @return array
     */
    protected function getAttributeValues()
    {
        $result = array();

        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $class = \XLite\Model\Attribute::getAttributeValueClass($type);
            $result[$type] = Database::getRepo($class)->findCommonValues($this->getProduct());
        }

        return $result;
    }

    /**
     * Apply attribute values changes
     *
     * @param array $oldValues Old values
     * @param array $newValues New values
     *
     * @return void
     */
    protected function applyAttributeValuesChanges(array $oldValues, array $newValues)
    {
        $diff = array();
        foreach (\XLite\Model\Attribute::getTypes() as $type => $name) {
            $class = \XLite\Model\Attribute::getAttributeValueClass($type);
            $diff += $class::getDiff($oldValues[$type], $newValues[$type]);
        }

        if ($diff) {
            ApplyAttrValuesGenerator::run(['attrsDiff' => $diff]);
        }
    }

    /**
     * Check - applying attribute values process is not-finished or not
     *
     * @return boolean
     */
    public function isApplyingAttrValuesNotFinished()
    {
        $eventName = ApplyAttrValuesGenerator::getEventName();
        $state = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS)
            )
            && !Database::getRepo('XLite\Model\TmpVar')
                        ->getVar(ApplyAttrValuesGenerator::getCancelFlagVarName());
    }

    /**
     * Cancel applying attribute values globally
     *
     * @return void
     */
    protected function doActionCancelApplyingAttrValues()
    {
        ApplyAttrValuesGenerator::cancel();
        \XLite\Core\TopMessage::addWarning('The global attributes changing process has been canceled.');
    }

    /**
     * Update product class
     *
     * @return void
     */
    protected function doActionUpdateProductClass()
    {
        $updateClass = false;

        if (-1 == \XLite\Core\Request::getInstance()->productClass) {
            $name = trim(\XLite\Core\Request::getInstance()->newProductClass);

            if ($name) {
                $productClass = new \XLite\Model\ProductClass;
                $productClass->setName($name);
                Database::getRepo('\XLite\Model\ProductClass')->insert($productClass);
                $updateClass = true;
            }

        } else {
            $productClass = Database::getRepo('\XLite\Model\ProductClass')->find(
                \XLite\Core\Request::getInstance()->productClass
            );
            $updateClass = true;
        }

        if ($updateClass) {
            $productClassChanged = $productClass
                && (
                    !$this->getProduct()->getProductClass()
                    || $productClass->getId() != $this->getProduct()->getProductClass()->getId()
                );

            $this->getProduct()->setProductClass($productClass);

            if ($productClassChanged) {
                Database::getRepo('\XLite\Model\Attribute')->generateAttributeValues(
                    $this->getProduct(),
                    true
                );
                Database::getRepo('\XLite\Model\AttributeProperty')->generateClassAttributeProperties(
                    $this->getProduct(),
                    $productClass
                );
            }

            Database::getEM()->flush();
            \XLite\Core\TopMessage::addInfo('Product class have been updated successfully');

        } else {
            \XLite\Core\TopMessage::addWarning('Product class name is empty');
        }
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->apply_attr_values_completed) {
            \XLite\Core\TopMessage::addInfo('The global attributes have been successfully changed.');

        } elseif ($request->apply_attr_values_failed) {
            \XLite\Core\TopMessage::addError('An error occurred during the global attributes changing.');
        }
    }

    // }}}
}
