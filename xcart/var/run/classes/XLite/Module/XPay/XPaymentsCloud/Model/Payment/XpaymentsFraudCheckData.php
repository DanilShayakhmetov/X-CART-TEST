<?php

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment;

use XLite\Model\Payment\Transaction;

/**
 * X-Payments Cloud payment fraud check data
 *
 * @Entity
 * @Table  (name="xpayments_fraud_check_data")
 */
class XpaymentsFraudCheckData extends \XLite\Model\AEntity
{
    /**
     * Result of the fraud check
     */
    const RESULT_UNKNOWN  = 0;
    const RESULT_ACCEPTED = 1;
    const RESULT_MANUAL   = 2;
    const RESULT_FAIL     = 3;
    const RESULT_PENDING  = 4;

    /**
     * Code defining anti fraud service/system
     */
    const CODE_ANTIFRAUD = 'antifraud';
    const CODE_GATEWAY   = 'gateway';
    const CODE_XPAYMENTS = 'xpayments';

    /**
     * Result messages
     */
    private $resultMessages = array(
        self::RESULT_FAIL     => 'High fraud risk detected',
        self::RESULT_ACCEPTED => 'Antifraud check passed',
        self::RESULT_MANUAL   => 'Manual review required',
        self::RESULT_PENDING  => 'Being reviewed',
    );

    /**
     * CSS class for score on the order details page
     */
    private $scoreClass = array(
        self::RESULT_FAIL     => 'danger',
        self::RESULT_ACCEPTED => 'success',
        self::RESULT_MANUAL   => 'warning',
        self::RESULT_PENDING  => 'warning',
    );

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code of the fraud check service/system
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $code = '';

    /**
     * Name of the fraud check service/system
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $service = '';

    /**
     * Name of the antifraud module
     *
     * @var string
     *
     * @Column (type="string", options={"default": ""})
     */
    protected $module = '';

    /**
     * Result of the fraud check
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $result = self::RESULT_UNKNOWN;

    /**
     * Status of the fraud check (as it's returned by the service/system)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $status = '';

    /**
     * Fraud score
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $score = 0;

    /**
     * Message
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $message = '';

    /**
     * Service transaction ID
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $serviceTransactionId = '';

    /**
     * URL (e.g. to the transaction)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $url = '';

    /**
     * List of errors
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $errors = '';

    /**
     * List of warnings
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $warnings = '';

    /**
     * List of triggered rules
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $rules = '';

    /**
     * Some other data
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $data = '';

    /**
     * Many-to-one relation with payment transaction
     *
     * @var Transaction
     *
     * @ManyToOne (targetEntity="XLite\Model\Payment\Transaction", inversedBy="xp_cloud_payment_fraud_check_data", fetch="LAZY", cascade={"merge","detach","persist"})
     * @JoinColumn (name="transaction_id", referencedColumnName="transaction_id")
     */
    protected $transaction;

    /**
     * Check if transaction is not yet reviewed by service
     *
     * @return bool
     */
    public function isPending()
    {
        return self::RESULT_PENDING == $this->getResult();
    }

    /**
     * Check if transaction is potentially fraudulent
     *
     * @return bool
     */
    public function isManualReview()
    {
        return self::RESULT_MANUAL == $this->getResult();
    }

    /**
     * True if fraud check returned error
     *
     * @return bool
     */
    public function isError()
    {
        return self::RESULT_UNKNOWN == $this->getResult();
    }

    /**
     * Get message to be displayed on the order details
     *
     * @return string
     */
    public function getDisplayMessage()
    {
        $message = $this->getMessage();

        if (array_key_exists($this->getResult(), $this->resultMessages)) {
            $message = $this->resultMessages[$this->getResult()];
        }

        return $message;
    }

    /**
     * Get CSS class for score
     *
     * @return string
     */
    public function getScoreClass()
    {
        $class = '';

        if (isset($this->scoreClass[$this->getResult()])) {
            $class = $this->scoreClass[$this->getResult()];
        }

        return $class;
    }

    /**
     * Return errors as an array
     *
     * return array()
     */
    public function getErrorsList()
    {
        return explode("\n", $this->getErrors());
    }

    /**
     * Return warnings as an array
     *
     * return array()
     */
    public function getWarningsList()
    {
        return explode("\n", $this->getWarnings());
    }

    /**
     * Return triggered rules as an array
     *
     * return array()
     */
    public function getRulesList()
    {
        $rules = explode("\n", $this->getRules());

        if (!empty($rules)) {
            foreach ($rules as $key => $rule) {

                // Remove rule ID from the beginning of rule description
                $rules[$key] = preg_replace('/^\d+ /', '', $rule);
            }
        }

        return $rules;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return XpaymentsFraudCheckData
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return XpaymentsFraudCheckData
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set module
     *
     * @param string $module
     * @return XpaymentsFraudCheckData
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set result
     *
     * @param integer $result
     * @return XpaymentsFraudCheckData
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get result
     *
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return XpaymentsFraudCheckData
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return XpaymentsFraudCheckData
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return XpaymentsFraudCheckData
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set serviceTransactionId
     *
     * @param string $serviceTransactionId
     * @return XpaymentsFraudCheckData
     */
    public function setServiceTransactionId($serviceTransactionId)
    {
        $this->serviceTransactionId = $serviceTransactionId;
        return $this;
    }

    /**
     * Get serviceTransactionId
     *
     * @return string
     */
    public function getServiceTransactionId()
    {
        return $this->serviceTransactionId;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return XpaymentsFraudCheckData
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set errors
     *
     * @param string $errors
     * @return XpaymentsFraudCheckData
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set warnings
     *
     * @param string $warnings
     * @return XpaymentsFraudCheckData
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;
        return $this;
    }

    /**
     * Get warnings
     *
     * @return string
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Set rules
     *
     * @param string $rules
     * @return XpaymentsFraudCheckData
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Get rules
     *
     * @return string
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return XpaymentsFraudCheckData
     */
    public function setData($data)
    {
        $this->data = serialize($data);
        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return @unserialize($this->data) ?: array();
    }

    /**
     * Set transaction
     *
     * @param Transaction $transaction
     * @return XpaymentsFraudCheckData
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
