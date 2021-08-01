<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AModifier;
use XCart\Bus\Query\Data\TagsDataSource;

class Language extends AModifier
{
    /**
     * @var array
     */
    private $tags;

    /**
     * @param Iterator       $iterator
     * @param string         $field
     * @param mixed          $data
     * @param TagsDataSource $tagsDataSource
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        TagsDataSource $tagsDataSource
    ) {
        parent::__construct($iterator, $field, $data);

        $this->tags = $tagsDataSource->getAll();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $translation = $this->getTranslation((array) $item->translations, $this->data);
        unset($item->translations);

        if ($translation) {
            $item->merge(array_filter($translation));
        }

        if ($item['tags']) {
            $item['tags'] = array_map(function ($item) {
                $item['name'] = $this->getTranslatedTag($item['id'], $this->data);

                return $item;
            }, $item['tags']);
        }

        return $item;
    }

    /**
     * @param array  $translations
     * @param string $language
     *
     * @return array|mixed
     */
    private function getTranslation(array $translations, $language)
    {
        foreach ($translations as $translation) {
            if ($translation['code'] === $language) {
                unset($translation['code']);

                return $translation;
            }
        }

        return [];
    }

    /**
     * @param string $tagId
     * @param string $language
     *
     * @return string
     */
    private function getTranslatedTag($tagId, $language)
    {
        foreach ($this->tags as $tag) {
            if ($tag['name'] === $tagId) {
                if (!empty($tag['translations'])) {
                    foreach ($tag['translations'] as $translation) {
                        if ($translation['code'] === $language) {
                            return $translation['tag_name'];
                        }
                    }
                }

                return $tag['name'];
            }
        }

        return $tagId;
    }
}
