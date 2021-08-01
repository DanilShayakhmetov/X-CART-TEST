<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core\IndexingEvent;

use XLite\Core\Database;
use XLite\Model\Attribute;
use XLite\Model\AttributeOption;
use XLite\Model\Profile;
use XLite\Module\XC\MultiVendor\Model\Vendor;
use XLite\Module\XC\ProductTags\Model\Tag;


class IndexingEventCore extends \XLite\Base\Singleton
{
    const MAX_RESULTS = 1000;

    public static function findProductIdsByAttribute(Attribute $attribute)
    {
        if (!$attribute) {
            return [];
        }

        $em = Database::getEM();

        switch ($attribute->getType()) {
            case Attribute::TYPE_SELECT:
                $query = $em->createQuery('SELECT DISTINCT p.product_id 
                               FROM XLite\Model\AttributeValue\AttributeValueSelect avs
                               JOIN avs.product p
                               WHERE avs.attribute = :attribute');
                break;

            case Attribute::TYPE_CHECKBOX:
                $query = $em->createQuery('SELECT DISTINCT p.product_id 
                               FROM XLite\Model\AttributeValue\AttributeValueCheckbox avc
                               JOIN avc.product p
                               WHERE avc.attribute = :attribute');
                break;

            case Attribute::TYPE_TEXT:
                $query = $em->createQuery('SELECT DISTINCT p.product_id 
                               FROM XLite\Model\AttributeValue\AttributeValueText avt
                               JOIN avt.product p
                               WHERE avt.attribute = :attribute AND avt.editable = false');
                break;

            default:
                return null;
        };

        $query->setParameter('attribute', $attribute);

        $result = $query->setMaxResults(self::MAX_RESULTS)->getResult();

        return array_map('current', $result);
    }

    public static function findProductIdsByAttributeOption(AttributeOption $attributeOption)
    {
        if (!$attributeOption) {
            return [];
        }

        $em = Database::getEM();

        $query = $em
            ->createQuery('SELECT DISTINCT p.product_id 
                               FROM XLite\Model\AttributeValue\AttributeValueSelect avs
                               JOIN avs.product p
                               WHERE avs.attribute_option = :attributeOption')
            ->setParameter('attributeOption', $attributeOption);

        $result = $query->setMaxResults(self::MAX_RESULTS)->getResult();

        return array_map('current', $result);
    }

    public static function findProductIdsByTag(Tag $tag)
    {
        if (!$tag) {
            return [];
        }

        $em = Database::getEM();

        $query = $em
            ->createQuery('SELECT DISTINCT p.product_id 
                               FROM XLite\Module\XC\ProductTags\Model\Tag t
                               JOIN t.products p
                               WHERE t = :tag')
            ->setParameter('tag', $tag);

        $result = $query->setMaxResults(self::MAX_RESULTS)->getResult();

        return array_map('current', $result);
    }

    public static function findProductIdsByVendor(Profile $vendor)
    {
        if (!$vendor) {
            return [];
        }

        $em = Database::getEM();

        $query = $em
            ->createQuery('SELECT p.product_id 
                               FROM XLite\Model\Product p
                               WHERE p.vendor = :vendor')
            ->setParameter('vendor', $vendor);

        $result = $query->setMaxResults(self::MAX_RESULTS)->getResult();

        return array_map('current', $result);
    }
}