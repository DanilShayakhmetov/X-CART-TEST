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

    changeItemsListBlankDescriptionTranslations();

    \XLite\Core\Database::getEM()->flush();
};

function changeItemsListBlankDescriptionTranslations()
{
    $qb = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabelTranslation')->createPureQueryBuilder('t');

    $translations = $qb -> select('t.label_id, t.label')
        -> where('t.label LIKE :text')
        -> setParameter('text', '%items-list-blank-description-heading%')
        -> getQuery()
        -> getArrayResult();

    foreach ($translations as $translation) {
        $translation['label'] = str_replace('<p class="items-list-blank-description-heading text-center">',
            '<p>', $translation['label']);
        $translation['label'] = str_replace("<p class='items-list-blank-description-heading text-center'>",
            '<p>', $translation['label']);
        $translation['label'] = str_replace('</p><p class="text-center">', ' ', $translation['label']);
        $translation['label'] = str_replace("</p><p class='text-center'>", ' ', $translation['label']);

        $qb = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabelTranslation')->createPureQueryBuilder('t');
        $qb -> update()
            -> set('t.label', ':newLabel')
            -> where('t.label_id = :labelId')
            -> setParameter('newLabel', $translation['label'])
            -> setParameter('labelId', $translation['label_id'])
            -> getQuery()
            -> execute();
    }
}
