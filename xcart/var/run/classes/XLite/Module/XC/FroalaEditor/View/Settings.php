<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FroalaEditor\View;

/**
 * Class Settings
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [ 'froala_settings' ]
        );
    }


    /**
     * Perform some operations when creating fields list by schema
     *
     * @param string $name Node name
     * @param array  $data Field description
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $result = parent::getFieldSchemaArgs($name, $data);

        if ($name === 'custom_colors') {
            $result[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'use_custom_colors' => [true],
                ],
            ];
        }

        return $result;
    }


    /**
     * getDefaultFieldValue
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        if( $name === 'custom_colors' && !$value) {
            $colors = [
                '61BD6D', '1ABC9C', '54ACD2', '2C82C9', '9365B8', '475577', 'CCCCCC',
                '41A85F', '00A885', '3D8EB9', '2969B0', '553982', '28324E', '000000',
                'F7DA64', 'FBA026', 'EB6B56', 'E25041', 'A38F84', 'EFEFEF', 'FFFFFF',
                'FAC51C', 'F37934', 'D14841', 'B8312F', '7C706B', 'D1D5D8',
            ];
            $value = implode(',', $colors);
        }

        return $value;
    }
}
