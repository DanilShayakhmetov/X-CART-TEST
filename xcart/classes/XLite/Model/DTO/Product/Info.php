<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Product;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Translation;
use XLite\Model\CategoryProducts;
use XLite\Model\DTO\Base\CommonCell;

class Info extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param Info                      $dto
     * @param ExecutionContextInterface $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
        if (!empty($dto->default->sku) && !static::isSKUValid($dto)) {
            static::addViolation($context, 'default.sku', Translation::lbl('SKU must be unique'));
        }

        if ($dto->marketing->meta_description_type === 'C' && '' === trim($dto->marketing->meta_description)) {
            static::addViolation($context, 'marketing.meta_description', Translation::lbl('This field is required'));
        }

        if (!static::isQtyValid($dto)) {
            static::addViolation($context, 'prices_and_inventory.inventory_tracking.quantity', Translation::lbl('Product quantity has changed'));
        }

        if (!$dto->marketing->clean_url->autogenerate) {
            $repo  = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
            if (!preg_match('/^' . $repo->getPattern('XLite\Model\Product') . '$/Sui', $dto->marketing->clean_url->clean_url)) {
                static::addViolation($context, 'marketing.clean_url.clean_url', Translation::lbl('The Clean URL entered has unallowed chars.'));
            }

            if (!$dto->marketing->clean_url->force) {
                $cleanUrlExt = $dto->marketing->clean_url->clean_url_ext ?: '';
                $value = $dto->marketing->clean_url->clean_url . $cleanUrlExt;

                if (!$repo->isURLUnique($value, 'XLite\Model\Product', $dto->default->identity)) {
                    $conflict = $repo->getConflict($value, 'XLite\Model\Product', $dto->default->identity);
                    if ($conflict instanceof \XLite\Model\TargetCleanUrl) {
                        static::addViolation($context, 'marketing.clean_url.clean_url', Translation::lbl('The Clean URL entered is already in use by target alias.'));
                    } elseif ($conflict instanceof \XLite\Model\RootDirCleanUrl) {
                        static::addViolation($context, 'marketing.clean_url.clean_url', Translation::lbl('The Clean URL you have specified matches the name of a folder on your server.'));
                    } else {
                        static::addViolation($context, 'marketing.clean_url.clean_url', Translation::lbl('The Clean URL entered is already in use.', ['entityURL' => $repo->buildEditURL($conflict)]));
                    }
                }
            }
        }
    }

    /**
     * @param Info $dto
     *
     * @return boolean
     */
    protected static function isSKUValid($dto)
    {
        $sku      = $dto->default->sku;
        $identity = $dto->default->identity;

        $entity = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($sku);

        return !$entity || (int) $entity->getProductId() === (int) $identity;
    }

    /**
     * @param Info $dto
     *
     * @return boolean
     */
    protected static function isQtyValid($dto)
    {
        $origin = $dto->prices_and_inventory->inventory_tracking->quantity_origin;
        if ($origin !== $dto->prices_and_inventory->inventory_tracking->quantity) {
            $current = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($dto->default->identity);
            if ($current && $origin != $current->getAmount()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $categories = [];
        foreach ($object->getCategories() as $category) {
            $categories[] = $category->getCategoryId();
        }

        $objectImages = $object->getImages();
        $images       = [0 => [
            'delete'   => false,
            'position' => '',
            'alt'      => '',
            'temp_id'  => '',
        ]];
        foreach ($objectImages as $image) {
            $images[$image->getId()] = [
                'delete'   => false,
                'position' => '',
                'alt'      => '',
                'temp_id'  => '',
            ];
        }

        $default       = [
            'identity' => $object->getProductId(),

            'name'                 => $object->getName(),
            'sku'                  => $object->getSku(),
            'images'               => $images,
            'category'             => $categories,
            'category_tree'        => $categories,
            'category_widget_type' => \XLite\Core\Request::getInstance()->product_modify_categroy_widget ?: 'search',
            'description'          => $object->getBriefDescription(),
            'full_description'     => $object->getDescription(),
            'available_for_sale'   => $object->getEnabled(),
        ];
        $this->default = new CommonCell($default);

        $memberships = [];
        foreach ($object->getMemberships() as $membership) {
            $memberships[] = $membership->getMembershipId();
        }

        $taxClass          = $object->getTaxClass();
        $qty               = $object->getAmount();
        $inventoryTracking = new CommonCell([
            'inventory_tracking' => $object->getInventoryEnabled(),
            'quantity'           => $qty,
            'quantity_origin'    => $qty,
        ]);

        $date = new \DateTime('now', \XLite\Core\Converter::getTimeZone());
        if ($object->getArrivalDate()) {
            $date->setTimestamp($object->getArrivalDate());
        }
        $date->modify('midnight');

        $pricesAndInventory         = [
            'memberships'        => $memberships,
            'tax_class'          => $taxClass ? $taxClass->getId() : null,
            'price'              => $object->getPrice(),
            'arrival_date'       => $date->getTimestamp(),
            'inventory_tracking' => $inventoryTracking,
        ];
        $this->prices_and_inventory = new CommonCell($pricesAndInventory);

        $shippingBox    = new CommonCell([
            'separate_box' => $object->getUseSeparateBox(),
            'dimensions'   => [
                'length' => $object->getBoxLength(),
                'width'  => $object->getBoxWidth(),
                'height' => $object->getBoxHeight(),
            ],
            'items_in_box' => $object->getItemsPerBox(),
        ]);
        $shipping       = [
            'weight'            => $object->getWeight(),
            'requires_shipping' => $object->getShippable(),
        ];
        $this->shipping = new CommonCell($shipping);

        static::compose(
            $this,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'shipping_box' => $shippingBox,
                    ],
                ],
            ]
        );

        $cleanUrlExt = \XLite\Model\Repo\CleanURL::isProductUrlHasExt() ? '.html' : '';
        if ($object->getCleanURL()
            && \XLite\Model\Repo\CleanURL::isProductUrlHasExt()
            && !preg_match('/.html$/', $object->getCleanURL())
        ) {
            $cleanUrlExt = '';
        }

        $cleanURL = new CommonCell([
            'autogenerate' => !(boolean) $object->getCleanURL(),
            'clean_url'    => \XLite\Model\Repo\CleanURL::isProductUrlHasExt()
                ? preg_replace('/.html$/', '', $object->getCleanURL())
                : $object->getCleanURL(),
            'force'        => false,
            'clean_url_ext' => $cleanUrlExt,
        ]);

        $marketing       = [
            'meta_description_type' => $object->getMetaDescType(),
            'meta_description'      => $object->getMetaDesc(),
            'meta_keywords'         => $object->getMetaTags(),
            'product_page_title'    => $object->getMetaTitle(),
            'clean_url'             => $cleanURL,
        ];
        $this->marketing = new CommonCell($marketing);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $object->setName((string) $default->name);

        $sku = trim($default->sku);
        $object->setSku((string) $sku);

        $object->processFiles('images', $default->images);

        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->findByIds($default->category);

        $order = array_flip($default->category);

        $object->replaceCategoryProductsLinksByCategories($categories, $default->category);

        foreach ($object->getCategoryProducts() as $categoryProductLink) {
            /** @var CategoryProducts $categoryProductLink */

            if (!$categoryProductLink->getCategory()) {
                continue;
            }

            $categoryId = $categoryProductLink->getCategory()->getCategoryId();

            if (isset($order[$categoryId])) {
                $categoryProductLink->setOrderbyInProduct($order[$categoryId] * 10);
            }
        }

        $description = $this->isContentTrustedByPermission('description')
            ? (string) $rawData['default']['description']
            : \XLite\Core\HTMLPurifier::purify((string) $rawData['default']['description']);

        $full_description = $this->isContentTrustedByPermission('full_description')
            ? (string) $rawData['default']['full_description']
            : \XLite\Core\HTMLPurifier::purify((string) $rawData['default']['full_description']);

        $object->setBriefDescription($description);
        $object->setDescription($full_description);

        $object->setEnabled((boolean) $default->available_for_sale);

        $priceAndInventory = $this->prices_and_inventory;
        $memberships       = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findByIds($priceAndInventory->memberships);
        $object->replaceMembershipsByMemberships($memberships);

        $taxClass = \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($priceAndInventory->tax_class);
        $object->setTaxClass($taxClass);

        $object->setPrice((float) $priceAndInventory->price);

        if ((int) $priceAndInventory->arrival_date) {
            $date = new \DateTime('now', \XLite\Core\Converter::getTimeZone());
            $date->setTimestamp($priceAndInventory->arrival_date);
            $date->modify('midnight');

            $object->setArrivalDate($date->getTimestamp());
        }

        $object->setInventoryEnabled((boolean) $priceAndInventory->inventory_tracking->inventory_tracking);

        $qty = $priceAndInventory->inventory_tracking->quantity;
        if ($priceAndInventory->inventory_tracking->quantity_origin !== $qty) {
            $object->setAmount((int) $qty);
        }

        $shipping = $this->shipping;
        $object->setWeight((float) $shipping->weight);
        $object->setShippable((boolean) $shipping->requires_shipping->requires_shipping);

        $shippingBox = $shipping->requires_shipping->shipping_box;
        $object->setUseSeparateBox((boolean) $shippingBox->separate_box);

        $object->setBoxLength($shippingBox->dimensions['length']);
        $object->setBoxWidth($shippingBox->dimensions['width']);
        $object->setBoxHeight($shippingBox->dimensions['height']);

        $object->setItemsPerBox($shipping->requires_shipping->shipping_box->items_in_box);

        $marketing = $this->marketing;
        $object->setMetaDescType($marketing->meta_description_type);

        if ($marketing->meta_description_type === \XLite\Model\Product::META_DESC_TYPE_AUTO) {
            $object->setMetaDesc($object->getMetaDesc());
        } else {
            $object->setMetaDesc((string)$marketing->meta_description);
        }

        $object->setMetaTags((string)$marketing->meta_keywords);
        $object->setMetaTitle((string)$marketing->product_page_title);

        if ($marketing->clean_url->autogenerate
            || empty($marketing->clean_url->clean_url)
        ) {
            $object->setCleanURL(\XLite\Core\Database::getRepo('XLite\Model\CleanURL')->generateCleanURL($object));

        } else {
            $cleanUrlExt = $marketing->clean_url->clean_url_ext ?: '';
            $value = $marketing->clean_url->clean_url . $cleanUrlExt;
            if ($marketing->clean_url->force) {
                $repo           = \XLite\Core\Database::getRepo('XLite\Model\CleanURL');
                $conflictEntity = $repo->getConflict(
                    $value,
                    $object,
                    $object->getProductId()
                );

                if ($conflictEntity && $value !== $conflictEntity->getCleanURL()) {
                    /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
                    $cleanURLs = $conflictEntity->getCleanURLs();
                    /** @var \XLite\Model\CleanURL $cleanURL */
                    foreach ($cleanURLs as $cleanURL) {
                        if ($value === $cleanURL->getCleanURL()) {
                            $cleanURLs->removeElement($cleanURL);
                            \XLite\Core\Database::getEM()->remove($cleanURL);

                            break;
                        }
                    }
                }

                $object->setCleanURL(
                    (string) $value,
                    !$conflictEntity
                    || !(
                        $conflictEntity instanceof \XLite\Model\TargetCleanUrl
                        || $conflictEntity instanceof \XLite\Model\RootDirCleanUrl
                    )
                );

            } else {
                $object->setCleanURL((string) $value);
            }

        }
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterUpdate($object, $rawData = null)
    {
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterPopulate($object, $rawData = null)
    {
        $object->updateQuickData();
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     */
    public function afterCreate($object, $rawData = null)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Attribute')->generateAttributeValues($object);

        if (!$object->getSku()) {
            $sku = \XLite\Core\Database::getRepo('XLite\Model\Product')->generateSKU($object);
            $object->setSku((string) $sku);
        }
    }
}
