<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData\Generators;

/**
 * Class Category
 * @package XLite\Console\Command\GenerateData\Generators
 */
class Category
{
    /**
     * @param        $suffix
     * @param        $pos
     * @param        $parent
     * @param bool   $generateImage
     * @param Image  $imageGenerator
     *
     * @return \XLite\Model\Category
     * @throws \Exception
     */
    public function generate($suffix, $pos, $parent, $generateImage = false, $imageGenerator)
    {
        /** @var \XLite\Model\Category $category */
        $category = $this->createCategory($suffix, $pos, $parent);

        if ($generateImage) {
            $image = new \XLite\Model\Image\Category\Image;
            $image->setCategory($category);
            $category->setImage($image);
            \XLite\Core\Database::getEM()->persist($image);
            $image->loadFromLocalFile($imageGenerator->generateImage());
        }

        return $category;
    }


    /**
     * @param $suffix
     * @param $pos
     * @param $parent
     *
     * @return \XLite\Model\Category
     */
    protected function createCategory($suffix, $pos, $parent)
    {
        /** @var \XLite\Model\Category $category */
        $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->insert(
            [
                'name'   => 'Test category #' . $suffix,
                'lpos'   => 0,
                'rpos'   => 0,
                'depth'  => 0,
                'pos'    => $pos,
                'parent' => $parent,
            ],
            false
        );

        if ($parent && !$parent->isPersistent()) {
            /** @var \XLite\Model\Category $parent */
            $category->setParent($parent);
            $parent->addChildren($category);
        }

        $quickFlags = new \XLite\Model\Category\QuickFlags();
        $quickFlags->setCategory($category);
        $category->setQuickFlags($quickFlags);

        return $category;
    }
}
