<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

interface IAnnotationService
{
    /**
     * @param string[] $classes
     */
    public function register($classes);
}
