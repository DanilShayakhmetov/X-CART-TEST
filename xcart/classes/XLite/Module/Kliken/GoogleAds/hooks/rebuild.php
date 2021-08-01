<?php

return new \XLite\Rebuild\Hook(
    function () {
        \XLite\Module\Kliken\GoogleAds\Logic\Helper::log('Rebuild.php is running...');

        \XLite\Module\Kliken\GoogleAds\Logic\Helper::postBackApiKeys();
    }
);
