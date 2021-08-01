<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';
    \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);

    \XLite\Core\Database::getEM()->flush();

    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

    foreach (['package_size', 'container_intl'] as $name) {
        $configEntry = $repo->findOneBy(array('name' => $name, 'category' => 'CDev\USPS'));

        if ($configEntry) {
            \XLite\Core\Database::getEM()->remove($configEntry);
        }
    }

    \XLite\Core\Config::updateInstance();
};
