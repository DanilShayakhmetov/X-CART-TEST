<?php

return new \XLite\Rebuild\Hook(
    function () {
        \XLite\Module\Kliken\GoogleAds\Logic\Helper::log('Install.php is running...');

        \XLite\Module\Kliken\GoogleAds\Logic\Helper::postBackApiKeys();
    }
);
