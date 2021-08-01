<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Event;
use XLite\Core\Request;
use XLite\Core\Templating\CacheManagerInterface;
use XLite\Core\Translation;
use XLite\Module\XC\ThemeTweaker\Core\TemplateObjectProvider;

/**
 * Theme tweaker template controller
 */
class ThemeTweakerTemplate extends \XLite\Controller\Admin\AAdmin
{
    const MAX_FILENAME_LENGTH = 255;

    public function __construct(array $params)
    {
        parent::__construct($params);

        if (Request::getInstance()->fromCustomer) {
            $this->hostRedirect = false;
        }

        $this->params = array_merge($this->params, ['id', 'template']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getTemplateLocalPath();
    }

    /**
     * Is create request
     *
     * @return boolean
     */
    public function isCreate()
    {
        return (bool) Request::getInstance()->template;
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {

            if (Request::getInstance()->isCreate) {

                echo <<<HTML
<script>window.opener.dispatchEvent(new Event('reload'));window.opener.location.reload();window.close()</script>
HTML;
                exit;

            }
        }
    }

    /**
     * Update model
     *
     * @return void
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function doActionApplyChanges()
    {
        $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
        $provider = $this->getTemplateObjectProvider();
        $rawData = Request::getInstance()->getPostData(false);
        $content = isset($rawData['content']) ? $rawData['content'] : null;
        $weight = \XLite\Core\Request::getInstance()->weight;
        $list = \XLite\Core\Request::getInstance()->list;
        $pendingId = \XLite\Core\Request::getInstance()->pendingId;

        if ($provider->getTemplatePath()) {
            $result = $this->updateEditedTemplate(
                $provider,
                $content,
                Request::getInstance()->interface,
                Request::getInstance()->innerInterface
            );

            if ($result) {
                if ($weight && $list) {
                    if ($pendingId) {
                        $this->updateListChild($provider->getTemplatePath(), $list, $weight);
                    } else {
                        $this->addListChild($provider->getTemplatePath(), $list, $weight);
                    }
                }

                $cacheDriver->delete(\XLite\Module\XC\ThemeTweaker\Core\Layout::THEME_TWEAKER_TEMPLATES_CACHE_KEY);
            }
        }

        $reverted = Request::getInstance()->reverted;

        if ($reverted) {
            Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->disableTemplates($reverted);
            $cacheDriver->delete(
                \XLite\Module\XC\ThemeTweaker\Core\Layout::THEME_TWEAKER_TEMPLATES_CACHE_KEY
            );
        }

        Database::getEM()->flush();

        $this->translateTopMessagesToHTTPHeaders();
        Event::getInstance()->display();
        Event::getInstance()->clear();
        $this->set('silent', true);
    }

    /**
     * @return TemplateObjectProvider
     */
    protected function getTemplateObjectProvider()
    {
        return TemplateObjectProvider::getInstance();
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * Tries to persist content changes into template entity
     *
     * @param TemplateObjectProvider $provider
     * @param string $content
     * @param string $interface
     * @param string $innerInterface
     * @return bool
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function updateEditedTemplate($provider, $content, $interface = \XLite::CUSTOMER_INTERFACE, $innerInterface = \XLite::CUSTOMER_INTERFACE)
    {
        $templatePath = $provider->getTemplatePath();
        $entity = $provider->getTemplateObject();

        if ($error = $this->validateTemplate($content, $templatePath)) {
            $this->throwError(
                $error['message'],
                400,
                $error['lineno']
            );

            return false;
        }

        /** @var \XLite\Module\XC\ThemeTweaker\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        if ($interface === \XLite::MAIL_INTERFACE) {
            $layout->setMailSkin($innerInterface);
        }

        $fullPath = $layout->getFullPathByLocalPath($templatePath, $interface);
        $skinRelativePath = $layout->getSkinRelativePathByLocalPath($templatePath, $interface);

        if (strlen($skinRelativePath) > static::MAX_FILENAME_LENGTH) {
            $this->throwError(
                Translation::lbl('File name is too long, it should be less than 255 characters'),
                400
            );
            return false;
        }

        if (\Includes\Utils\FileManager::write($fullPath, $content)) {
            if (!$entity->isPersistent()) {
                $this->removePossibleDuplicates($skinRelativePath);
            }

            $this->getTemplateCacheManager()->invalidate($fullPath);

            $entity->setDate(LC_START_TIME);
            $entity->setTemplate($skinRelativePath);
            $entity->setEnabled(true);

            Database::getEM()->persist($entity);
            Database::getEM()->flush();
        } else {
            $this->throwError(
                Translation::lbl('Not enough permissions to update the template file'),
                500
            );
            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @param int $code
     * @param int $lineNo
     */
    protected function throwError($message, $code = 400, $lineNo = null)
    {
        $params = [
            'message' => $message
        ];

        if ($lineNo) {
            $params['line'] = $lineNo;
        }

        $this->headerStatus($code);
        Event::getInstance()->trigger('themetweaker.error', $params);
    }

    /**
     * Add list child record when new template is added via editor
     *
     * @param $templatePath
     * @param $list
     * @param $weight
     */
    protected function addListChild($templatePath, $list, $weight)
    {
        $skins = \XLite\Core\Layout::getInstance()->getSkins(\XLite\Model\ViewList::INTERFACE_CUSTOMER);
        $relativePath = null;

        foreach ($skins as $skin) {
            if (strpos($templatePath, $skin) === 0) {
                $relativePath = trim(str_replace($skin, '', $templatePath), LC_DS);
                break;
            }
        }

        if ($relativePath) {
            \XLite\Core\Layout::getInstance()->addTemplateToList(
                $relativePath,
                $list,
                [
                    'zone'   => \XLite\Model\ViewList::INTERFACE_CUSTOMER,
                    'weight' => $weight,
                    'weight_override' => $weight,
                    'override_mode' => \XLite\Model\ViewList::OVERRIDE_MOVE,
                ]
            );

            $this->removeListCache($list);
        }
    }

    /**
     * Add list child record when new template is added via editor
     *
     * @param $list
     */
    protected function removeListCache($list)
    {
        \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->deleteCacheByNameAndParams(
            'class_list',
            [
                'list' => $list,
                'zone' => \XLite\Model\ViewList::INTERFACE_CUSTOMER
            ]
        );
    }


    /**
     * Add list child record when new template is added via editor
     *
     * @param $templatePath
     * @param $list
     * @param $weight
     */
    protected function updateListChild($templatePath, $list, $weight)
    {
        $skins = \XLite\Core\Layout::getInstance()->getSkins(\XLite\Model\ViewList::INTERFACE_CUSTOMER);
        $relativePath = null;

        foreach ($skins as $skin) {
            if (strpos($templatePath, $skin) === 0) {
                $relativePath = trim(str_replace($skin, '', $templatePath), LC_DS);
                break;
            }
        }

        $entity = Database::getRepo('XLite\Model\ViewList')->findEqualByData([
            'tpl'    => $relativePath,
            'list'   => $list
        ]);

        if ($entity) {
            $entity->setWeight($weight);
        }
    }

    /**
     * @param $fullPath
     */
    protected function removePossibleDuplicates($fullPath)
    {
        Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->deleteByPath($fullPath);
    }

    /**
     * Validates the template syntax and returns the array of errors or null value if the syntax is fine.
     * @param $content
     * @param $identifier
     * @return array|null
     */
    public function validateTemplate($content, $identifier)
    {
        $engine = \XLite::getInstance()->getContainer()->make('templating_engine');

        try {
            $node = $engine->parse($engine->tokenize($content, $identifier));
        } catch (\Exception $e) {
            return [
                'message' => $e->getMessage(),
                'lineno'  => $e->getTemplateLine()
            ];
        }

        return null;
    }

    /**
     * Returns a (cached) templating engine instance
     *
     * @return CacheManagerInterface
     */
    protected function getTemplateCacheManager()
    {
        return $this->getContainer()->get('template_cache_manager');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\Model\Template';
    }

    /**
     * Returns current template short path
     *
     * @return string
     */
    public function getTemplateLocalPath()
    {
        $localPath = '';

        if ($this->isCreate()) {
            $localPath = Request::getInstance()->template;
        } elseif (Request::getInstance()->id) {
            $template = Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')
                ->find(Request::getInstance()->id);

            $localPath = $template ? $template->getTemplate() : '';
        }

        return $localPath;
    }
}
