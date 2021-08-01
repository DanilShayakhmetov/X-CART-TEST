<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    $scopes = \XLite\Core\Config::getInstance()->CDev->Paypal->loginScopes;
    if ($scopes) {
        $scopes = @unserialize($scopes);
    }

    $deleteScopes = ['phone', 'https://uri.paypal.com/services/expresscheckout'];

    foreach ($deleteScopes as $scope) {
        $key = array_search($scope, $scopes, true);
        if ($key !== false) {
            unset($scopes[$key]);
        }
    }

    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
        'category' => 'CDev\Paypal',
        'name'     => 'loginScopes',
        'value'    => serialize($scopes),
    ]);

    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    \XLite\Core\Database::getEM()->flush();
};
