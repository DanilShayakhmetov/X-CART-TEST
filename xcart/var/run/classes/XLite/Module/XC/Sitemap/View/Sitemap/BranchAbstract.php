<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Sitemap\View\Sitemap;

/**
 *  This widget draws a tree's branch
 */
abstract class BranchAbstract extends \XLite\View\AView
{
    /**
     * Widget parameter
     */
    const PARAM_TYPE  = 'type';
    const PARAM_ID    = 'id';
    const PARAM_LEVEL = 'level';

    /**
     * Page types
     */
    const PAGE_CATEGORY = 'C';
    const PAGE_STATIC   = 'S';
    const PAGE_PRODUCT  = 'P';

    /**
     * Level limit 
     */
    const LEVEL_LIMIT = 3;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TYPE => new \XLite\Model\WidgetParam\TypeString(
                'Page type', null, false
            ),
            static::PARAM_ID => new \XLite\Model\WidgetParam\TypeInt(
                'Page ID', 0, false
            ),
            static::PARAM_LEVEL => new \XLite\Model\WidgetParam\TypeInt(
                'Level', 0, false
            ),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Sitemap/branch.twig';
    }

    /**
     * Check - display child or not
     * 
     * @param string  $type Page type
     * @param integer $id   Page ID
     *  
     * @return boolean
     */
    protected function isDisplayChild($type, $id)
    {
        return $this->getLevel() < static::LEVEL_LIMIT
            && $this->hasChild($type, $id);
    }

    /**
     * Return existence of children of this category
     * 
     * @param string  $type Page type
     * @param integer $id   Page ID
     *  
     * @return boolean
     */
    protected function hasChild($type, $id)
    {
        $result = false;
        if (static::PAGE_CATEGORY == $type) {
            $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);

            if ($category && count($category->getChildren()) > 0) {
                $result = true;

            } elseif ($this->isDisplayCategoryProducts($id)) {
                $cnd = new \XLite\Core\CommonCell;
                $cnd->categoryId = $id;
                $result = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, true) > 0;
            }

        } elseif (static::PAGE_STATIC == $type) {
            $result = in_array($id, [1, 2]);
        }

        return $result;
    }

    /**
     * Return content of this branch
     *
     * @return array
     */
    protected function getBranch()
    {
        return $this->getChildren(
            $this->getParam(static::PARAM_TYPE),
            $this->getParam(static::PARAM_ID)
        );
    }

    /**
     * Get children
     * 
     * @param string  $type Page type
     * @param integer $id   Page ID
     *  
     * @return array
     */
    protected function getChildren($type, $id)
    {
        $result = [];

        if (empty($type)) {

            // Root level
            $result = [
                [
                    'type' => static::PAGE_CATEGORY,
                    'id'   => \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId(),
                    'name' => static::t('Catalog'),
                ],
                [
                    'type' => static::PAGE_STATIC,
                    'id'   => 1,
                    'name' => static::t('Order'),
                ],
                [
                    'type' => static::PAGE_STATIC,
                    'id'   => 2,
                    'name' => static::t('Account'),
                ],
            ];

        } elseif ($type == static::PAGE_STATIC) {

            // Static pages
            if (1 == $id)  {
                $result = [
                    [
                        'type' => static::PAGE_STATIC,
                        'id'   => 101,
                        'name' => static::t('Cart'),
                        'url'  => static::buildURL('cart'),
                    ],
                    [
                        'type' => static::PAGE_STATIC,
                        'id'   => 102,
                        'name' => static::t('Checkout'),
                        'url'  => static::buildURL('checkout'),
                    ],
                ];

            } elseif (2 == $id) {

                if (!\XLite\Core\Auth::getInstance()->isLogged()) {
                    $result = [
                        [
                            'type' => static::PAGE_STATIC,
                            'id'   => 201,
                            'name' => static::t('Sign in'),
                            'url'  => static::buildURL('login'),
                        ],
                        [
                            'type' => static::PAGE_STATIC,
                            'id'   => 202,
                            'name' => static::t('Register'),
                            'url'  => static::buildURL('profile', null, ['mode' => 'register']),
                        ],
                    ];

                } else {
                    $result = [
                        [
                            'type' => static::PAGE_STATIC,
                            'id'   => 251,
                            'name' => static::t('My account'),
                            'url'  => static::buildURL('profile'),
                        ],
                        [
                            'type' => static::PAGE_STATIC,
                            'id'   => 252,
                            'name' => static::t('Orders'),
                            'url'  => static::buildURL('order_list'),
                        ],
                        [
                            'type' => static::PAGE_STATIC,
                            'id'   => 253,
                            'name' => static::t('Address book'),
                            'url'  => static::buildURL('address_book'),
                        ],
                    ];
                }
            }

        } elseif ($type == static::PAGE_CATEGORY) {

            // Subcategories
            $result = [];
            $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);
            if ($category) {
                foreach ($category->getChildren() as $cat) {                    
                    if ($cat && $cat->isVisible()) {
                        $result[] = [
                            'type' => static::PAGE_CATEGORY,
                            'id'   => $cat->getCategoryId(),
                            'name' => $cat->getName(),
                            'url'  => static::buildURL('category', null, ['category_id' => $cat->getCategoryId()]),
                        ];
                    }
                }
            }

            // ... + products
            if ($this->isDisplayCategoryProducts($this->getParam(static::PARAM_ID))) {
                $cnd = new \XLite\Core\CommonCell;
                $cnd->categoryId = $this->getParam(static::PARAM_ID);
                foreach (\XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd) as $product) {
                    if ($product && $product->isVisible()) {
                        $result[] = [
                            'type' => static::PAGE_PRODUCT,
                            'id'   => $product->getProductId(),
                            'name' => $product->getName(),
                            'url'  => static::buildURL(
                                'product',
                                null,
                                [
                                    'product_id'  => $product->getProductId(),
                                    'category_id' => $this->getParam(static::PARAM_ID),
                                ]
                            ),
                        ];
                    }
                }
            }

        }

        return $result;
    }

    /**
     * Get level
     *
     * @return integer
     */
    protected function getLevel()
    {
        return $this->getParam(static::PARAM_LEVEL);
    }

    /**
     * Get next level 
     * 
     * @return integer
     */
    protected function getNextLevel()
    {
        return $this->getLevel() + 1;
    }

    /**
     * Get container tag attributes 
     * 
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [
            'class' => [
                'level-' . $this->getLevel(),
                'page-type-' . $this->getParam(static::PARAM_TYPE),
                'page-id-' . $this->getParam(static::PARAM_ID),
            ],
        ];
    }

    /**
     * Get item container tag attributes 
     * 
     * @param string  $type Page type
     * @param integer $id   Page ID
     *  
     * @return array
     */
    protected function getItemContainerTagAttributes($type, $id)
    {
        return [
            'class'     => [
                'page-type-' . $type,
                'page-id-' . $id,
            ],
            'data-type' => $type,
            'data-id'   => $id,
        ];
    }

    /**
     * Check - display category products or not
     * 
     * @param integer $categoryId Category ID
     *  
     * @return boolean
     */
    protected function isDisplayCategoryProducts($categoryId)
    {
        return false;
    }

}
