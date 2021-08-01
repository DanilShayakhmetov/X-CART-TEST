<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Profile;

use XLite\Core\Database;

/**
 * \XLite\View\Model\Profile\Main
 */
abstract class MainAbstract extends \XLite\View\Model\Profile\AProfile
{
    /**
     * Form sections
     */
    const SECTION_MAIN = 'main';

    /**
     * Schema of the "E-mail & Password" section
     *
     * @var array
     */
    protected $mainSchema = [
        'login'                 => [
            self::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Text\Email',
            self::SCHEMA_LABEL            => 'E-mail',
            self::SCHEMA_REQUIRED         => true,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password'              => [
            self::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL            => 'New password',
            self::SCHEMA_REQUIRED         => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'password_conf'         => [
            self::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL            => 'Confirm password',
            self::SCHEMA_REQUIRED         => false,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'membership_id'         => [
            self::SCHEMA_CLASS                                 => 'XLite\View\FormField\Input\Text',
            \XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES => [
                'readonly' => true,
                'disabled' => true,
            ],
            self::SCHEMA_LABEL                                 => 'Membership',
            self::SCHEMA_REQUIRED                              => false,
        ],
        'pending_membership_id' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Membership',
            self::SCHEMA_LABEL    => 'Pending membership',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Return value for the "register" mode param
     *
     * @return string
     */
    public static function getRegisterMode()
    {
        return \XLite\Controller\Admin\Profile::getRegisterMode();
    }

    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->sections = $this->getProfileMainSections() + $this->sections;

        parent::__construct($params, $sections);
    }

    /**
     * The "mode" parameter used to determine if we create new or modify existing profile
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return self::getRegisterMode() === \XLite\Core\Request::getInstance()->mode;
    }

    /**
     * Return current profile ID
     *
     * @param boolean $checkMode Check mode or not OPTIONAL
     *
     * @return integer
     */
    public function getProfileId($checkMode = true)
    {
        return ($this->isRegisterMode() && $checkMode) ?: parent::getProfileId();
    }

    /**
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        $validActions = ['validateInput', 'delete'];

        return in_array($this->currentAction, $validActions, true) ?: parent::isValid();
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/profile/main.css';

        return $list;
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

        switch ($name) {
            case 'membership_id':
                $value = 0 < $value ? $this->getModelObject()->getMembership()->getName() : static::t('none');
                break;

            case 'pending_membership_id':
                if ($this->getModelObject()->getMembership()) {
                    $value = 0 < $value ? $this->getModelObject()->getPendingMembership()->getName() : static::t('none');
                }
                break;

            default:
        }

        return $value;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Profile\Main';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Profile details';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' profile-form-container';
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        // Create new profile - password is required
        if (!$this->getModelObject()->isPersistent()) {
            foreach (['password', 'password_conf'] as $field) {
                if (isset($this->mainSchema[$field])) {
                    $this->mainSchema[$field][self::SCHEMA_REQUIRED] = true;
                }
            }

            unset($this->mainSchema['membership_id']);
        }

        if ($this->getModelObject()->getMembership()) {
            unset($this->mainSchema['pending_membership_id']);
        }

        if (!\XLite\Core\Config::getInstance()->General->allow_membership_request) {
            if (!$this->getModelObject()->getMembership()) {
                unset($this->mainSchema['membership_id']);
            }
            unset($this->mainSchema['pending_membership_id']);
        }

        if ($this->isRegisterMode()) {
            $this->mainSchema['password'][self::SCHEMA_LABEL] = 'Password';
        }

        return $this->getFieldsBySchema($this->mainSchema);
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = \XLite\Core\Auth::encryptPassword($data['password']);

        } elseif (isset($data['password'])) {
            unset($data['password']);
        }

        parent::setModelProperties($data);
    }

    /**
     * Check password and its confirmation
     * TODO: simplify
     *
     * @return boolean
     */
    protected function checkPassword()
    {
        $result = true;
        $data   = $this->getRequestData();

        if (isset($this->sections[self::SECTION_MAIN])
            && (!empty($data['password']) || !empty($data['password_conf']))
            && $data['password'] != $data['password_conf']
        ) {
            $result     = false;
            $formFields = $this->getFormFields();
            $this->addErrorMessage(
                'password',
                'Password and its confirmation do not match',
                $formFields[self::SECTION_MAIN]
            );
        }

        return $result;
    }

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getProfileMainSections()
    {
        return [
            self::SECTION_MAIN => 'Personal info',
        ];
    }

    /**
     * Return error message for the "validateInput" action
     *
     * @param string $login Profile login
     *
     * @return string
     */
    protected function getErrorActionValidateInputMessage($login)
    {
        return 'The <i>' . $login . '</i> profile is already registered. '
            . 'Please, try some other email address.';
    }

    /**
     * Process the errors occurred during the "validateInput" action
     *
     * @return void
     */
    protected function postprocessErrorActionValidateInput()
    {
        \XLite\Core\TopMessage::addError(
            $this->getErrorActionValidateInputMessage($this->getRequestData('login'))
        );
    }

    /**
     * Create profile
     *
     * @return boolean
     */
    protected function performActionCreate()
    {
        $result = false;

        if ($this->checkPassword()) {
            $result = parent::performActionCreate();
        }

        if ($result) {
            \XLite\Core\TmpVars::getInstance()->pendingMembershipsUpdateTimestamp = LC_START_TIME;
        }

        return $result;
    }

    /**
     * Update profile
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        $data   = $this->getRequestData();
        $result = $this->checkPassword() ? parent::performActionUpdate() : false;

        if ($result
            && (!empty($data['password']) || !empty($data['login']))
            && $profile = $this->getModelObject()
        ) {
            $profile->logoffSessions(true);
        }

        return $result;
    }

    /**
     * Perform certain action for the model object
     * User can modify only his own profile or create a new one
     *
     * @return boolean
     */
    protected function performActionValidateInput()
    {
        $result = true;

        // Get profile by login (email)
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->findByLogin($this->getModelObject()->getLogin());

        // Check if found profile is the same as a modified profile object
        if (isset($profile)) {
            $result = $profile->getProfileId() === $this->getModelObject()->getProfileId();
        }

        return $result;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        if ($this->isLogged()) {
            $result['delete_profile'] = new \XLite\View\Button\DeleteUser();
        }

        return $result;
    }

    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        $duplicate = Database::getRepo('XLite\Model\Profile')
            ->checkRegisteredUserWithSameLogin($this->getModelObject());

        if ($duplicate) {
            $this->addErrorMessage('login', static::t(
                'This e-mail address is already in use by another user.'
            ));
        }
    }

    /**
     * Prepare request data for mapping profile object
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        if (isset($data['pending_membership_id']) && 0 < (int) ($data['pending_membership_id'])) {
            $membership = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($data['pending_membership_id']);

            if (isset($membership)) {
                $data['pending_membership'] = $membership;
            }
        }

        if (!isset($data['pending_membership'])) {
            $data['pending_membership'] = null;
        }

        return $data;
    }
}
