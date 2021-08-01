<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\SchemaList;

class Cores extends AAPIRequest
{
    public const FORMAT_OPTION_CURRENT_MINOR_CORE_VERSION = 'current_minor_core_version';

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_CORES;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(
            [
                Constant::FIELD_VERSION       => [
                    'filter'  => \FILTER_VALIDATE_REGEXP,
                    'flags'   => \FILTER_REQUIRE_ARRAY,
                    'options' => ['regexp' => Constant::REGEXP_VERSION],
                ],
                Constant::FIELD_REVISION_DATE => \FILTER_VALIDATE_INT,
                Constant::FIELD_LENGTH        => [
                    'filter'  => \FILTER_VALIDATE_INT,
                    'options' => ['min_range' => 0],
                ],
                Constant::FIELD_WAVE          => \FILTER_VALIDATE_INT,
            ]
        );
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        $result = [];

        $key = 'CDev-Core';

        foreach ((array) $data as $core) {
            $version = $core[Constant::FIELD_VERSION];

            list($system, $major) = array_map('intval', explode('.', $version[Constant::FIELD_VERSION_MAJOR]));
            $minor = (int) $version[Constant::FIELD_VERSION_MINOR];
            $build = !empty($version[Constant::FIELD_VERSION_BUILD])
                ? (int) $version[Constant::FIELD_VERSION_BUILD]
                : 0;

            $result[$key][] = [
                'id' => $key,

                'author' => 'CDev',
                'name'   => 'Core',

                'rating'    => 0,
                'votes'     => 0,
                'downloads' => 0,

                'price'    => 0,
                'currency' => '',

                'version' => sprintf('%s.%s.%s.%s', $system, $major, $minor, $build),

                'minorRequiredCoreVersion' => 0,
                'dependencies'             => [
                    'dependsOn'        => [],
                    'incompatibleWith' => [],
                    'requiredBy'       => [],
                ],

                'revisionDate' => $core[Constant::FIELD_REVISION_DATE],

                'isLanding'       => false,
                'landingPosition' => -1,
                'salesChannelPos' => -1,

                'displayAuthor' => 'X-Cart team',
                'displayName'   => 'Core',
                'description'   => '',
                'tags'          => [],
                'icon'          => '',
                'pageURL'       => '',
                'authorPageURL' => '',
                'authorEmail'   => '',
                'hasLicense'    => '',
                'translations'  => [],

                'packSize'     => $core[Constant::FIELD_LENGTH],
                'isSystem'     => true,
                'xcnPlan'      => '',
                'editionState' => '',
                'editions'     => [],
                'xbProductId'  => '',
                'private'      => '',
                'wave'         => $core[Constant::FIELD_WAVE],
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getDefaultConfig(): array
    {
        return [
            self::FORMAT_OPTION_CURRENT_MINOR_CORE_VERSION => null,
        ];
    }
}
