<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\ITransport;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Schema;

class Addons extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_ADDONS_LIST;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema([
            Constant::FIELD_MODULES => [
                'flags'  => \FILTER_REQUIRE_ARRAY,
            ],
            Constant::FIELD_INFO => [
                'flags'  => \FILTER_REQUIRE_ARRAY,
            ],
        ]);
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        $result = [
            Constant::FIELD_INFO => $data[Constant::FIELD_INFO],
        ];

        foreach ((array) $data[Constant::FIELD_MODULES] as $module) {
            // Module key fields
            $author = static::getElement($module, Constant::FIELD_AUTHOR);
            $name   = static::getElement($module, Constant::FIELD_NAME);

            // Module versions
            $majorVersion = static::getElement($module, Constant::FIELD_VERSION, Constant::FIELD_VERSION_MAJOR);
            $minorVersion = static::getElement($module, Constant::FIELD_VERSION, Constant::FIELD_VERSION_MINOR);
            $buildVersion = static::getElement($module, Constant::FIELD_VERSION, Constant::FIELD_VERSION_BUILD) ?: 0;

            [$coreVersion, $majorVersion] = explode('.', $majorVersion);

            // Short names
            $key = $author . '-' . $name;

            $dependsOn = array_map(function ($module) {
                $parts = explode('\\', $module);

                return $parts[0] . '-' . $parts[1];
            }, (array) static::getElement($module, Constant::FIELD_DEPENDENCIES));

            $translations = [];
            foreach ((array) static::getElement($module, Constant::FIELD_TRANSLATIONS) as $code => $fields) {
                $translations[] = [
                    'code'        => $code,
                    'moduleName'  => $fields[Constant::FIELD_READABLE_NAME] ?? '',
                    'description' => $fields[Constant::FIELD_DESCRIPTION] ?? '',
                ];
            }

            $tags = array_map(function($tag) {
                return html_entity_decode($tag);
            }, static::getElement($module, Constant::FIELD_TAGS));

            $result[Constant::FIELD_MODULES][$key][] = [
                'id'                       => $key,
                'version'                  => sprintf('%s.%s.%s.%s', $coreVersion, $majorVersion, $minorVersion, $buildVersion),
                'author'                   => $author,
                'name'                     => $name,
                'authorName'               => static::getElement($module, Constant::FIELD_READABLE_AUTHOR),
                'moduleName'               => static::getElement($module, Constant::FIELD_READABLE_NAME),
                'description'              => html_entity_decode(static::getElement($module, Constant::FIELD_DESCRIPTION)),
                'minorRequiredCoreVersion' => static::getElement($module, Constant::FIELD_MIN_CORE_VERSION),
                'dependsOn'                => $dependsOn,
                'isSystem'                 => (bool) static::getElement($module, Constant::FIELD_IS_SYSTEM),
                'icon'                     => preg_replace('/^https?:/', '', static::getElement($module, Constant::FIELD_ICON_URL)),
                'listIcon'                 => preg_replace('/^https?:/', '', static::getElement($module, Constant::FIELD_LIST_ICON_URL)),
                'skinPreview'              => preg_replace('/^https?:/', '', static::getElement($module, 'skin_list_image')),
                'pageURL'                  => static::getElement($module, Constant::FIELD_PAGE_URL),
                'authorPageURL'            => static::getElement($module, Constant::FIELD_AUTHOR_PAGE_URL),
                'authorEmail'              => static::getElement($module, Constant::FIELD_AUTHOR_EMAIL),
                'revisionDate'             => static::getElement($module, Constant::FIELD_REVISION_DATE),
                'price'                    => static::getElement($module, Constant::FIELD_PRICE),
                'origPrice'                => static::getElement($module, Constant::FIELD_ORIG_PRICE) ?: static::getElement($module, Constant::FIELD_PRICE),
                'currency'                 => static::getElement($module, Constant::FIELD_CURRENCY),
                'downloads'                => static::getElement($module, Constant::FIELD_DOWNLOADS_COUNT),
                'rating'                   => static::getElement($module, Constant::FIELD_RATING, Constant::FIELD_RATING_RATE),
                'votes'                    => static::getElement($module, Constant::FIELD_RATING, Constant::FIELD_RATING_VOTES_COUNT),
                'tags'                     => $tags,
                'translations'             => $translations,
                'editions'                 => (array) static::getElement($module, Constant::FIELD_EDITIONS),
                'editionState'             => static::getElement($module, Constant::FIELD_EDITION_STATE),
                'hasLicense'               => static::getElement($module, Constant::FIELD_HAS_LICENSE),
                'xcnPlan'                  => static::getElement($module, Constant::FIELD_XCN_PLAN),
                'wave'                     => static::getElement($module, Constant::FIELD_WAVE) ?: 0,
                'private'                  => static::getElement($module, Constant::FIELD_PRIVATE),
                'xbProductId'              => static::getElement($module, Constant::FIELD_XB_PRODUCT_ID),
                'packSize'                 => static::getElement($module, Constant::FIELD_LENGTH),
                'salesChannelPos'          => static::getElement($module, Constant::FIELD_SALES_CHANNEL_POS) ?: 0,
                'isLanding'                => (bool) static::getElement($module, Constant::FIELD_IS_LANDING),
                'landingPosition'          => static::getElement($module, Constant::FIELD_LANDING_POSITION),
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_MODULES => serialize([]),
        ];
    }
}
