<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Translation;

/**
 * Languages list
 */
class Languages extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/languages/controller.js';

        return $list;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Language';
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'languages';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Language\Admin';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME     => static::t('Language'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/languages/cell.name.twig',
                static::COLUMN_ORDERBY  => 100,
            ),
            'code' => array(
                static::COLUMN_NAME => static::t('Code'),
                static::COLUMN_ORDERBY  => 200,
            ),
            'defaultCustomer' => array(
                static::COLUMN_NAME  => static::t('Customer area'),
                static::COLUMN_SUBHEADER => static::t('Default: {{code}}',
                    ['code' => mb_strtoupper(\XLite\Core\Config::getInstance()->General->default_language)]),
                static::COLUMN_CLASS => \XLite\View\FormField\Inline\Input\Radio\Radio::class,
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_PARAMS => array(
                    'fieldName' => 'defaultCustomer',
                ),
                static::COLUMN_ORDERBY  => 300,
            ),
            'defaultAdmin' => array(
                static::COLUMN_NAME => static::t('Admin panel'),
                static::COLUMN_SUBHEADER => static::t('Default: {{code}}',
                    ['code' => mb_strtoupper(\XLite\Core\Config::getInstance()->General->default_admin_language)]),
                static::COLUMN_CLASS => \XLite\View\FormField\Inline\Input\Radio\Radio::class,
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_PARAMS => array(
                    'fieldName' => 'defaultAdmin',
                ),
                static::COLUMN_ORDERBY  => 400,
            ),
            'labels_count' => array(
                static::COLUMN_NAME => static::t('Labels'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/languages/cell.labels.twig',
                static::COLUMN_LINK => 'language',
                static::COLUMN_HEAD_HELP => $this->getColumnLabelsCountHelp(),
                static::COLUMN_ORDERBY  => 250,
            ),
            'countries' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Countries'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_CLASS   => \XLite\View\FormField\Inline\Select\Select2\Countries::class,
                static::COLUMN_ORDERBY => 800,
            ],
        );
    }

    /**
     * Labels count column head help text
     *
     * @return string
     */
    protected function getColumnLabelsCountHelp()
    {
        return static::t('Displays the number of labels translated to the language');
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array('languages');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' languages';
    }

    /**
     * Return languages list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $languages = \XLite\Core\Database::getRepo(\XLite\Model\Language::class)->findAddedLanguages();

        return $countOnly ? count($languages) : $languages;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Return true if entity is used as a default for admin or customer interfaces
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function isUsedAsDefault($entity)
    {
        return $entity->getDefaultCustomer() || $entity->getDefaultAdmin();
    }

    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Get column CSS class for specific entity
     *
     * @param array                 $column Column data
     * @param \XLite\Model\Language $entity Language object
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if (in_array($column[static::COLUMN_CODE], array('defaultCustomer', 'defaultAdmin'), true)
            && !$entity->getEnabled()
        ) {
            $class .= ' disabled';
        }

        return $class;
    }

    /**
     * Remove language entity
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        return $this->isAllowEntityRemove($entity) && $entity->setAdded(false) && $entity->removeTranslations();
    }

    /**The English language cannot be removed as it is primary language for all texts.
     * Disable removing English language (as all texts are hardcoded in English)
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return 'en' !== $entity->getCode() && !$entity->getValidModule();
    }

    /**
     * Get count of labels for specific language
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function getLabelsCount(\XLite\Model\AEntity $entity)
    {
        return \XLite\Core\Database::getRepo(\XLite\Model\LanguageLabel::class)->countByCode($entity->getCode());
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        $list = parent::getRightActions();

        array_unshift($list, $this->getDir() . '/' . $this->getPageBodyDir() . '/languages/action.csv.twig');
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/languages/action.help.twig';

        return $list;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);

        if ($entity && !$entity->getEnabled()) {
            $classes[] = 'lock';
        }

        return $classes;
    }

    /**
     * Get specific language help message
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return string
     */
    protected function getLanguageHelpMessage(\XLite\Model\Language $entity)
    {
        $message = null;

        if ($entity->getValidModule()) {
            $module = \Includes\Utils\Module\Manager::getRegistry()->getModule($entity->getModule());

            $moduleName = sprintf('%s (%s)', $module->moduleName, $module->authorName);
            $message = static::t('This language is added by module and cannot be removed.', array('module' => $moduleName));

        } elseif ('en' === $entity->getCode()) {
            $message = static::t('The English language cannot be removed as it is primary language for all texts.');
        }

        return $message;
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return static::t('X languages have been removed', array('count' => $count));
    }

    /**
     * Returns list of available country codes
     *
     * @return array
     */
    protected function getUnavailableCountries()
    {
        $unavailableCountries = \XLite\Core\Database::getRepo(\XLite\Model\Country::class)->findAllEnabledCountriesWithActiveLanguage();

        $result = array_map(function ($value) {
            return $value['code'];
        }, $unavailableCountries);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function displayCommentedData(array $data)
    {
        $data['unavailableCountries'] = $this->getUnavailableCountries();

        parent::displayCommentedData($data);
    }
}
