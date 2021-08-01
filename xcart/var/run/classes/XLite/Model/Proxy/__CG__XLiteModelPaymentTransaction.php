<?php

namespace XLite\Model\Proxy\__CG__\XLite\Model\Payment;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Proxy\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array<string, null> properties to be lazy loaded, indexed by property name
     */
    public static $lazyPropertiesNames = array (
);

    /**
     * @var array<string, mixed> default values of properties to be lazy loaded, with keys being the property names
     *
     * @see \Doctrine\Common\Proxy\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array (
);



    public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * {@inheritDoc}
     * @param string $name
     */
    public function __get($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', [$name]);

        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', [$name, $value]);

        return parent::__set($name, $value);
    }

    /**
     * {@inheritDoc}
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', [$name]);

        return parent::__isset($name);

    }

    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'xpaymentsFraudCheckData', 'transaction_id', 'date', 'publicTxnId', 'method_name', 'method_local_name', 'status', 'value', 'note', 'type', 'public_id', 'order', 'payment_method', 'data', 'backend_transactions', 'currency', 'readableStatuses', 'registeredCache', '_previous_state'];
        }

        return ['__isInitialized__', 'xpaymentsFraudCheckData', 'transaction_id', 'date', 'publicTxnId', 'method_name', 'method_local_name', 'status', 'value', 'note', 'type', 'public_id', 'order', 'payment_method', 'data', 'backend_transactions', 'currency', 'readableStatuses', 'registeredCache', '_previous_state'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Transaction $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy::$lazyPropertiesDefaults as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @deprecated no longer in use - generated code now relies on internal components rather than generated public API
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function isByPayPal()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isByPayPal', []);

        return parent::isByPayPal();
    }

    /**
     * {@inheritDoc}
     */
    public function setBraintreeDataCell($field, $value, $title, $fieldPrefix = '', $titlePrefix = '', $titlePostfix = '')
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBraintreeDataCell', [$field, $value, $title, $fieldPrefix, $titlePrefix, $titlePostfix]);

        return parent::setBraintreeDataCell($field, $value, $title, $fieldPrefix, $titlePrefix, $titlePostfix);
    }

    /**
     * {@inheritDoc}
     */
    public function setBraintreeLiteralDataCell($field, $value, $title)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBraintreeLiteralDataCell', [$field, $value, $title]);

        return parent::setBraintreeLiteralDataCell($field, $value, $title);
    }

    /**
     * {@inheritDoc}
     */
    public function setBraintreeCreditCardDataCell($field, $value, $title)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBraintreeCreditCardDataCell', [$field, $value, $title]);

        return parent::setBraintreeCreditCardDataCell($field, $value, $title);
    }

    /**
     * {@inheritDoc}
     */
    public function setBraintreePayPalDataCell($field, $value, $title)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBraintreePayPalDataCell', [$field, $value, $title]);

        return parent::setBraintreePayPalDataCell($field, $value, $title);
    }

    /**
     * {@inheritDoc}
     */
    public function markBraintreeProcessed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'markBraintreeProcessed', []);

        return parent::markBraintreeProcessed();
    }

    /**
     * {@inheritDoc}
     */
    public function isBraintreeProcessed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isBraintreeProcessed', []);

        return parent::isBraintreeProcessed();
    }

    /**
     * {@inheritDoc}
     */
    public function isBraintreeTransaction()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isBraintreeTransaction', []);

        return parent::isBraintreeTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function getBraintreeDataCell($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBraintreeDataCell', [$name]);

        return parent::getBraintreeDataCell($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionData($strict = false)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTransactionData', [$strict]);

        return parent::getTransactionData($strict);
    }

    /**
     * {@inheritDoc}
     */
    public function isXpayments()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isXpayments', []);

        return parent::isXpayments();
    }

    /**
     * {@inheritDoc}
     */
    public function setXpaymentsId($xpid)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setXpaymentsId', [$xpid]);

        return parent::setXpaymentsId($xpid);
    }

    /**
     * {@inheritDoc}
     */
    public function getXpaymentsId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getXpaymentsId', []);

        return parent::getXpaymentsId();
    }

    /**
     * {@inheritDoc}
     */
    public function getChargeValueModifier()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getChargeValueModifier', []);

        return parent::getChargeValueModifier();
    }

    /**
     * {@inheritDoc}
     */
    public function isAccepted()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isAccepted', []);

        return parent::isAccepted();
    }

    /**
     * {@inheritDoc}
     */
    public function isDeclined()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isDeclined', []);

        return parent::isDeclined();
    }

    /**
     * {@inheritDoc}
     */
    public function addXpaymentsFraudCheckData(\XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData $fraudCheckData)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addXpaymentsFraudCheckData', [$fraudCheckData]);

        return parent::addXpaymentsFraudCheckData($fraudCheckData);
    }

    /**
     * {@inheritDoc}
     */
    public function getXpaymentsFraudCheckData()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getXpaymentsFraudCheckData', []);

        return parent::getXpaymentsFraudCheckData();
    }

    /**
     * {@inheritDoc}
     */
    public function getProfile()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProfile', []);

        return parent::getProfile();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrigProfile()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrigProfile', []);

        return parent::getOrigProfile();
    }

    /**
     * {@inheritDoc}
     */
    public function prepareBeforeCreate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'prepareBeforeCreate', []);

        return parent::prepareBeforeCreate();
    }

    /**
     * {@inheritDoc}
     */
    public function generateTransactionId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'generateTransactionId', []);

        return parent::generateTransactionId();
    }

    /**
     * {@inheritDoc}
     */
    public function renewTransactionId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'renewTransactionId', []);

        return parent::renewTransactionId();
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setValue', [$value]);

        return parent::setValue($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentMethod(\XLite\Model\Payment\Method $method = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPaymentMethod', [$method]);

        return parent::setPaymentMethod($method);
    }

    /**
     * {@inheritDoc}
     */
    public function updateValue(\XLite\Model\Order $order)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'updateValue', [$order]);

        return parent::updateValue($order);
    }

    /**
     * {@inheritDoc}
     */
    public function handleCheckoutAction()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'handleCheckoutAction', []);

        return parent::handleCheckoutAction();
    }

    /**
     * {@inheritDoc}
     */
    public function isOpen()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOpen', []);

        return parent::isOpen();
    }

    /**
     * {@inheritDoc}
     */
    public function isCanceled()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCanceled', []);

        return parent::isCanceled();
    }

    /**
     * {@inheritDoc}
     */
    public function isFailed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isFailed', []);

        return parent::isFailed();
    }

    /**
     * {@inheritDoc}
     */
    public function isCompleted()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCompleted', []);

        return parent::isCompleted();
    }

    /**
     * {@inheritDoc}
     */
    public function isInProgress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isInProgress', []);

        return parent::isInProgress();
    }

    /**
     * {@inheritDoc}
     */
    public function isPending()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isPending', []);

        return parent::isPending();
    }

    /**
     * {@inheritDoc}
     */
    public function isVoid()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isVoid', []);

        return parent::isVoid();
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthorized()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isAuthorized', []);

        return parent::isAuthorized();
    }

    /**
     * {@inheritDoc}
     */
    public function isCaptured()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCaptured', []);

        return parent::isCaptured();
    }

    /**
     * {@inheritDoc}
     */
    public function isRefunded()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRefunded', []);

        return parent::isRefunded();
    }

    /**
     * {@inheritDoc}
     */
    public function isRefundedNotMulti()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRefundedNotMulti', []);

        return parent::isRefundedNotMulti();
    }

    /**
     * {@inheritDoc}
     */
    public function isCaptureTransactionAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCaptureTransactionAllowed', []);

        return parent::isCaptureTransactionAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function isVoidTransactionAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isVoidTransactionAllowed', []);

        return parent::isVoidTransactionAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function isRefundTransactionAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRefundTransactionAllowed', []);

        return parent::isRefundTransactionAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function isRefundPartTransactionAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRefundPartTransactionAllowed', []);

        return parent::isRefundPartTransactionAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function isRefundMultiTransactionAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRefundMultiTransactionAllowed', []);

        return parent::isRefundMultiTransactionAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function getReadableStatus($status = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getReadableStatus', [$status]);

        return parent::getReadableStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function setDataCell($name, $value, $label = NULL, $accessLevel = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDataCell', [$name, $value, $label, $accessLevel]);

        return parent::setDataCell($name, $value, $label, $accessLevel);
    }

    /**
     * {@inheritDoc}
     */
    public function getDataCell($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDataCell', [$name]);

        return parent::getDataCell($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getDetail($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDetail', [$name]);

        return parent::getDetail($name);
    }

    /**
     * {@inheritDoc}
     */
    public function createBackendTransaction($transactionType)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'createBackendTransaction', [$transactionType]);

        return parent::createBackendTransaction($transactionType);
    }

    /**
     * {@inheritDoc}
     */
    public function getInitialBackendTransaction()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInitialBackendTransaction', []);

        return parent::getInitialBackendTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function registerTransactionInOrderHistory($suffix = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'registerTransactionInOrderHistory', [$suffix]);

        return parent::registerTransactionInOrderHistory($suffix);
    }

    /**
     * {@inheritDoc}
     */
    public function getHistoryEventDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHistoryEventDescription', []);

        return parent::getHistoryEventDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function getHistoryEventDescriptionData()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHistoryEventDescriptionData', []);

        return parent::getHistoryEventDescriptionData();
    }

    /**
     * {@inheritDoc}
     */
    public function getEventData()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEventData', []);

        return parent::getEventData();
    }

    /**
     * {@inheritDoc}
     */
    public function isSameMethod(\XLite\Model\Payment\Method $method)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isSameMethod', [$method]);

        return parent::isSameMethod($method);
    }

    /**
     * {@inheritDoc}
     */
    public function cloneEntity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'cloneEntity', []);

        return parent::cloneEntity();
    }

    /**
     * {@inheritDoc}
     */
    public function getNote()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNote', []);

        return parent::getNote();
    }

    /**
     * {@inheritDoc}
     */
    public function getCartItems()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCartItems', []);

        return parent::getCartItems();
    }

    /**
     * {@inheritDoc}
     */
    public function getTransactionId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTransactionId', []);

        return parent::getTransactionId();
    }

    /**
     * {@inheritDoc}
     */
    public function setDate($date)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDate', [$date]);

        return parent::setDate($date);
    }

    /**
     * {@inheritDoc}
     */
    public function getDate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDate', []);

        return parent::getDate();
    }

    /**
     * {@inheritDoc}
     */
    public function setPublicTxnId($publicTxnId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPublicTxnId', [$publicTxnId]);

        return parent::setPublicTxnId($publicTxnId);
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicTxnId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPublicTxnId', []);

        return parent::getPublicTxnId();
    }

    /**
     * {@inheritDoc}
     */
    public function setMethodName($methodName)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMethodName', [$methodName]);

        return parent::setMethodName($methodName);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMethodName', []);

        return parent::getMethodName();
    }

    /**
     * {@inheritDoc}
     */
    public function setMethodLocalName($methodLocalName)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMethodLocalName', [$methodLocalName]);

        return parent::setMethodLocalName($methodLocalName);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodLocalName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMethodLocalName', []);

        return parent::getMethodLocalName();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getValue', []);

        return parent::getValue();
    }

    /**
     * {@inheritDoc}
     */
    public function setNote($note)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNote', [$note]);

        return parent::setNote($note);
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', [$type]);

        return parent::setType($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getType', []);

        return parent::getType();
    }

    /**
     * {@inheritDoc}
     */
    public function setPublicId($publicId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPublicId', [$publicId]);

        return parent::setPublicId($publicId);
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPublicId', []);

        return parent::getPublicId();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrder(\XLite\Model\Order $order = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrder', [$order]);

        return parent::setOrder($order);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrder', []);

        return parent::getOrder();
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentMethod()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPaymentMethod', []);

        return parent::getPaymentMethod();
    }

    /**
     * {@inheritDoc}
     */
    public function addData(\XLite\Model\Payment\TransactionData $data)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addData', [$data]);

        return parent::addData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getData', []);

        return parent::getData();
    }

    /**
     * {@inheritDoc}
     */
    public function addBackendTransactions(\XLite\Model\Payment\BackendTransaction $backendTransactions)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addBackendTransactions', [$backendTransactions]);

        return parent::addBackendTransactions($backendTransactions);
    }

    /**
     * {@inheritDoc}
     */
    public function getBackendTransactions()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBackendTransactions', []);

        return parent::getBackendTransactions();
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrency(\XLite\Model\Currency $currency = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrency', [$currency]);

        return parent::setCurrency($currency);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrency', []);

        return parent::getCurrency();
    }

    /**
     * {@inheritDoc}
     */
    public function buildDataForREST($withAssociations = true)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'buildDataForREST', [$withAssociations]);

        return parent::buildDataForREST($withAssociations);
    }

    /**
     * {@inheritDoc}
     */
    public function getModelAssociationsForREST()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModelAssociationsForREST', []);

        return parent::getModelAssociationsForREST();
    }

    /**
     * {@inheritDoc}
     */
    public function _getPreviousState()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '_getPreviousState', []);

        return parent::_getPreviousState();
    }

    /**
     * {@inheritDoc}
     */
    public function map(array $data)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'map', [$data]);

        return parent::map($data);
    }

    /**
     * {@inheritDoc}
     */
    public function __unset($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__unset', [$name]);

        return parent::__unset($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRepository', []);

        return parent::getRepository();
    }

    /**
     * {@inheritDoc}
     */
    public function checkCache()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'checkCache', []);

        return parent::checkCache();
    }

    /**
     * {@inheritDoc}
     */
    public function detach()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'detach', []);

        return parent::detach();
    }

    /**
     * {@inheritDoc}
     */
    public function __call($method, array $args = array (
))
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__call', [$method, $args]);

        return parent::__call($method, $args);
    }

    /**
     * {@inheritDoc}
     */
    public function isPropertyExists($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isPropertyExists', [$name]);

        return parent::isPropertyExists($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setterProperty($property, $value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setterProperty', [$property, $value]);

        return parent::setterProperty($property, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getterProperty($property)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getterProperty', [$property]);

        return parent::getterProperty($property);
    }

    /**
     * {@inheritDoc}
     */
    public function isPersistent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isPersistent', []);

        return parent::isPersistent();
    }

    /**
     * {@inheritDoc}
     */
    public function isDetached()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isDetached', []);

        return parent::isDetached();
    }

    /**
     * {@inheritDoc}
     */
    public function isManaged()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isManaged', []);

        return parent::isManaged();
    }

    /**
     * {@inheritDoc}
     */
    public function getUniqueIdentifierName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUniqueIdentifierName', []);

        return parent::getUniqueIdentifierName();
    }

    /**
     * {@inheritDoc}
     */
    public function getUniqueIdentifier()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUniqueIdentifier', []);

        return parent::getUniqueIdentifier();
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEntityName', []);

        return parent::getEntityName();
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldMetadata($property)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFieldMetadata', [$property]);

        return parent::getFieldMetadata($property);
    }

    /**
     * {@inheritDoc}
     */
    public function update()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'update', []);

        return parent::update();
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'create', []);

        return parent::create();
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'delete', []);

        return parent::delete();
    }

    /**
     * {@inheritDoc}
     */
    public function processFiles($field, array $data)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'processFiles', [$field, $data]);

        return parent::processFiles($field, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldsDefinition($class = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFieldsDefinition', [$class]);

        return parent::getFieldsDefinition($class);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareEntityBeforeCommit($type)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'prepareEntityBeforeCommit', [$type]);

        return parent::prepareEntityBeforeCommit($type);
    }

    /**
     * {@inheritDoc}
     */
    public function isSerializable()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isSerializable', []);

        return parent::isSerializable();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', []);

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityLock($type = 'lock', $ttl = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEntityLock', [$type, $ttl]);

        return parent::setEntityLock($type, $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function isEntityLocked($type = 'lock')
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isEntityLocked', [$type]);

        return parent::isEntityLocked($type);
    }

    /**
     * {@inheritDoc}
     */
    public function isEntityLockExpired($type = 'lock')
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isEntityLockExpired', [$type]);

        return parent::isEntityLockExpired($type);
    }

    /**
     * {@inheritDoc}
     */
    public function unsetEntityLock($type = 'lock')
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'unsetEntityLock', [$type]);

        return parent::unsetEntityLock($type);
    }

}