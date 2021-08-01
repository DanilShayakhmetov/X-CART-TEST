<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Settings\Notification;

use XLite\Core\Auth;

class Notification extends \XLite\View\FormModel\AFormModel
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'notification/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        $list[] = 'notification/help.css';

        return $list;
    }

    /**
     * @return string|null
     */
    protected function getTarget()
    {
        return 'notification';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        return [
            'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
            'page'               => $this->getDataObject()->default->page,
        ];
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        return [
            'settings'        => [
                'label'    => static::t('Settings'),
                'position' => 100,
            ],
            'scheme'          => [
                'label'    => static::t('Scheme'),
                'position' => 200,
            ],
            'system_settings' => [
                'label'    => static::t('System settings'),
                'collapse' => true,
                'expanded' => false,
                'position' => 300,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $available = $this->getDataObject()->settings->available;

        $help = $this->getWidget(
            [
                'template' => 'notification/help.twig',
            ]
        )->getContent();

        return [
            'settings'        => [
                'status'  => [
                    'label'       => static::t('Notification state'),
                    'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                    'on_caption'  => 'checkbox.onoff.on',
                    'off_caption' => 'checkbox.onoff.off',
                    'disabled'    => !$available,
                    'position'    => 100,
                ],
                'subject' => [
                    'label'    => static::t('Subject'),
                    'help'     => $help,
                    'position' => 200,
                ],
            ],
            'scheme'          => [
                'header'    => [
                    'label'    => static::t('Header'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 100,
                ],
                'greeting'  => [
                    'label'    => static::t('Greeting'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 200,
                ],
                'text'      => [
                    'label'    => static::t('Text'),
                    'type'     => 'XLite\View\FormModel\Type\MailTextareaAdvancedType',
                    'help'     => $help,
                    'position' => 300,
                ],
                'body'      => [
                    'label'    => static::t('Dynamic message'),
                    'position' => 400,
                    'help'     => static::t('This content shows via %dynamic_message% variable. Do not use this variable to put content (if it exists) below the main message'),
                ],
                'signature' => [
                    'label'    => static::t('Signature'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'fields'   => [
                        'status' => [
                            'type'        => 'XLite\View\FormModel\Type\SwitcherType',
                            'on_caption'  => 'checkbox.onoff.on',
                            'off_caption' => 'checkbox.onoff.off',
                            'position'    => 100,
                        ],
                        'link'   => [
                            'show_label_block' => false,
                            'position'         => 200,
                        ],
                    ],
                    'position' => 500,
                ],
            ],
            'system_settings' => [
                'name'        => [
                    'label'       => static::t('Name'),
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                    ],
                    'position'    => 100,
                ],
                'description' => [
                    'label'       => static::t('Description'),
                    'type'        => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                    ],
                    'position'    => 200,
                ],
            ],
        ];
    }

    /**
     * Return form theme files. Used in template.
     *
     * @return array
     */
    protected function getFormThemeFiles()
    {
        $list = parent::getFormThemeFiles();
        $list[] = 'form_model/settings/notification/notification.twig';

        return $list;
    }

    /**
     * Return all variables
     *
     * @return array
     */
    protected function getVariables()
    {
        $variables = \XLite\Core\Mail\Registry::getNotificationVariables(
            $this->getDataObject()->default->templatesDirectory,
            $this->getDataObject() instanceof \XLite\Model\DTO\Settings\Notification\Admin
                ? \XLite::ADMIN_INTERFACE
                : \XLite::CUSTOMER_INTERFACE
        );

        return $variables;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['preview'] = new \XLite\View\Button\Link(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Preview full email'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'model-button always-enabled',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->getPreviewURL(),
                \XLite\View\Button\Link::PARAM_BLANK    => true,
            ]
        );

        $result['send_test_email'] = new \XLite\View\Button\Link(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                \XLite\View\Button\AButton::PARAM_STYLE => 'model-button always-enabled',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->getSendTestEmailURL(),
            ]
        );

        return $result;
    }

    /**
     * Get Preview template URL
     *
     * @return string
     */
    protected function getPreviewURL()
    {
        return $this->buildURL(
            'notification',
            '',
            [
                'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
                'page'               => $this->getDataObject() instanceof \XLite\Model\DTO\Settings\Notification\Admin
                    ? \XLite::ADMIN_INTERFACE
                    : \XLite::CUSTOMER_INTERFACE,
                'preview'            => true,
            ]
        );
    }

    /**
     * Get Send test email URL
     *
     * @return string
     */
    protected function getSendTestEmailURL()
    {
        return $this->buildURL(
            'notification',
            'send_test_email',
            [
                'templatesDirectory' => $this->getDataObject()->default->templatesDirectory,
                'page'               => $this->getDataObject() instanceof \XLite\Model\DTO\Settings\Notification\Admin
                    ? \XLite::ADMIN_INTERFACE
                    : \XLite::CUSTOMER_INTERFACE,
                'from_notification'  => 1,
            ]
        );
    }
}
