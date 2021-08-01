<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\Model\Payment\Processor;

/**
 * Authorize.net accept.js payment processor
 */
class AuthorizenetAcceptjs extends \XLite\Model\Payment\Base\Online
{

    /**
     * AVS messages
     *
     * @var string[]
     */
    protected $avsMessages = array(
        'A' => 'Address (Street) matches, ZIP does not.',
        'B' => 'Address information not provided for AVS check.',
        'E' => 'AVS error.',
        'G' => 'Non-U.S. Card Issuing Bank.',
        'N' => 'No Match on Address (Street) or ZIP.',
        'P' => 'AVS not applicable for this transaction.',
        'R' => 'Retry—System unavailable or timed out.',
        'S' => 'Service not supported by issuer.',
        'U' => 'Address information is unavailable.',
        'W' => 'Nine digit ZIP matches, Address (Street) does not.',
        'X' => 'Address (Street) and nine digit ZIP match.',
        'Y' => 'Address (Street) and five digit ZIP match.',
        'Z' => 'Five digit ZIP matches, Address (Street) does not.',
    );

    /**
     * CVV messages
     *
     * @var string[]
     */
    protected $cvvMessages = array(
        'M' => 'Match.',
        'N' => 'No Match.',
        'P' => 'Not Processed.',
        'S' => 'Should have been present.',
        'U' => 'Issuer unable to process request.',
    );

    /**
     * CAVV messages
     *
     * @var string[]
     */
    protected $cavvMessages = array(
        ''  => 'CAVV not validated.',
        '0' => 'CAVV not validated because erroneous data was submitted.',
        '1' => 'CAVV failed validation.',
        '2' => 'CAVV passed validation.',
        '3' => 'CAVV validation could not be performed; issuer attempt incomplete.',
        '4' => 'CAVV validation could not be performed; issuer system error.',
        '5' => 'Reserved for future use.',
        '6' => 'Reserved for future use.',
        '7' => 'CAVV attempt—failed validation—issuer available (U.S.-issued card/non-U.S acquirer).',
        '8' => 'CAVV attempt—passed validation—issuer available (U.S.-issued card/non-U.S. acquirer).',
        '9' => 'CAVV attempt—failed validation—issuer unavailable (U.S.-issued card/non-U.S. acquirer).',
        'A' => 'CAVV attempt—passed validation—issuer unavailable (U.S.-issued card/non-U.S. acquirer).',
        'B' => 'CAVV passed validation, information only, no liability shift.',
    );

    /**
     * @inheritdoc
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return $method->getSetting('api_login_id')
            && $method->getSetting('transaction_key')
            && $method->getSetting('public_key');
    }

    /**
     * @inheritdoc
     */
    public function getAvailableSettings()
    {
        return array(
            'api_login_id',
            'transaction_key',
            'public_key',
            'type',
            'mode',
            'prefix',
        );
    }

    /**
     * @inheritdoc
     */
    public function getAllowedTransactions()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettingsWidget()
    {
        return 'modules/QSL/AuthorizenetAcceptjs/config.twig';
    }

    /**
     * @inheritdoc
     */
    public function getInputTemplate()
    {
        return 'modules/QSL/AuthorizenetAcceptjs/payment.twig';
    }

    /**
     * @inheritdoc
     */
    public function getInputErrors(array $data)
    {
        $errors = parent::getInputErrors($data);

        if (empty($data['dataDescriptor']) || empty($data['dataValue'])) {
            $errors[] = \XLite\Core\Translation::lbl(
                'Payment processed with errors. Please, try again or ask administrator'
            );
            $this->log('Invalid input data: ' . var_export($data, true), true);
        }

        return $errors;
    }

    /**
     * @inheritdoc
     */
    public function getInitialTransactionType($method = null)
    {
        $type = $method ? $method->getSetting('type') : $this->getSetting('type');

        return 'sale' == $type
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;
    }

    /**
     * @inheritdoc
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Check capture (partially) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isCapturePartTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $this->isCaptureTransactionAllowed($transaction);
    }

    /**
     * Check capture (multiple) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isCaptureMultiTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $this->isCaptureTransactionAllowed($transaction);
    }

    /**
     * Check capture operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isCaptureTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        $rid = $transaction->getDetail('transId');

        return $transaction->isCaptureTransactionAllowed() && $rid;
    }

    /**
     * Check void operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isVoidTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isVoidTransactionAllowed()
            && $transaction->getDetail('transId');
    }

    /**
     * Check refund (partially) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isRefundPartTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $this->isRefundTransactionAllowed($transaction);
    }

    /**
     * Check refund (multiple) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isRefundMultiTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $this->isRefundTransactionAllowed($transaction);
    }

    /**
     * Check refund operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isRefundTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        $rid = $transaction->getDetail('transId');
        $last_4_digits = $transaction->getDetail('last_4_digits');

        return $transaction->isCaptured() && $transaction->isCompleted() && $rid && $last_4_digits;
    }

    /**
     * @inheritdoc
     */
    protected function doInitialPayment()
    {
        $api_login_id = $this->getSetting('api_login_id');
        $transaction_key = $this->getSetting('transaction_key');
        $dataDescriptor = $this->request['dataDescriptor'];
        $dataValue = $this->request['dataValue'];
        $total = $this->formatCurrency($this->transaction->getValue());
        $type = $this->isCapture()
            ? 'authCaptureTransaction'
            : 'authOnlyTransaction';
        $txnid = substr($this->getTransactionId(), 0, 20);

        $lineItems = $this->generateLineItems();
        $customer = $this->generateCustomer();
        $sid = $this->getSetting('mode') == 'test'
            ? 'AAA100302'
            : 'AAA105360';

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication>
        <name>$api_login_id</name>
        <transactionKey>$transaction_key</transactionKey>
    </merchantAuthentication>
    <transactionRequest>
        <transactionType>$type</transactionType>
        <amount>$total</amount>
        <payment>
            <opaqueData>
                <dataDescriptor>$dataDescriptor</dataDescriptor>
                <dataValue>$dataValue</dataValue >
            </opaqueData>
        </payment>
        <solution>
            <id>$sid</id>
        </solution>
        <order>
            <invoiceNumber>$txnid</invoiceNumber>
        </order>
        $lineItems
        $customer
    </transactionRequest>
</createTransactionRequest>
XML;
        $this->log('Request: ' . $this->getAPIUrl() . PHP_EOL . $xml);

        $request = new \XLite\Core\HTTP\Request($this->getAPIUrl());
        $request->verb = 'POST';
        $request->body = $xml;
        $response = $request->sendRequest();

        $this->log('Response: ' . $response->body);

        $messages = array();

        $bom = pack('H*','FEFF');
        $rxml = @simplexml_load_string(preg_replace('/^' . $bom . '/', '', $response->body));

        if ((string)$rxml->messages->resultCode != 'Ok' || !in_array((string)$rxml->transactionResponse->responseCode, array('1', '4'))) {

            if ($rxml->transactionResponse->errors && $rxml->transactionResponse->errors->error) {
                foreach ($rxml->transactionResponse->errors->error as $error) {
                    $messages[] = (string)$error->errorText;
                }

            } else {
                foreach ($rxml->messages->message as $n) {
                    $messages[] = (string)$n->text;
                }
                if ((string)$rxml->transactionResponse->responseCode) {
                    $codes = [
                        2 => 'Declined',
                        3 => 'Error',
                    ];
                    $messages[] = 'Result status: ' . $codes[(int)$rxml->transactionResponse->responseCode];
                }
            }

            $result = static::FAILED;

        } else {
            foreach ($rxml->transactionResponse->messages->message as $n) {
                $messages[] = (string)$n->description;
            }

            $this->setDetail('transId', (string)$rxml->transactionResponse->transId, 'Transaction ID');
            $this->setDetail('last_4_digits', substr((string)$rxml->transactionResponse->accountNumber, -4), 'Last 4 CC digits');

            $backendTransaction = $this->registerBackendTransaction();
            $backendTransaction->setDataCell('authcode', (string)$rxml->transactionResponse->authCode);
            $backendTransaction->setDataCell('transId', (string)$rxml->transactionResponse->transId);
            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('initial request');

            $avsResultCode = (string)$rxml->transactionResponse->avsResultCode;
            if (isset($this->avsMessages[$avsResultCode])) {
                $this->transaction->setDataCell('avsResult', $this->avsMessages[$avsResultCode], 'Address Verification Service response');

            } elseif ($avsResultCode) {
                $this->transaction->setDataCell('avsResultCode', $avsResultCode, 'Address Verification Service response code');
            }

            $cvvResultCode = (string)$rxml->transactionResponse->cvvResultCode;
            if (isset($this->cvvMessages[$cvvResultCode])) {
                $this->transaction->setDataCell('cvvResult', $this->cvvMessages[$cvvResultCode], 'Card code verification response');

            } elseif ($cvvResultCode) {
                $this->transaction->setDataCell('cvvResultCode', $cvvResultCode, 'Card code verification response code');
            }

            $cavvResultCode = (string)$rxml->transactionResponse->cavvResultCode;
            if (isset($this->cavvMessages[$cavvResultCode])) {
                $this->transaction->setDataCell('cavvResult', $this->cavvMessages[$cavvResultCode], 'Cardholder authentication verification response');

            } elseif ($cavvResultCode) {
                $this->transaction->setDataCell('cavvResultCode', $cavvResultCode, 'Cardholder authentication verification response code');
            }

            $result = $rxml->transactionResponse->responseCode == '4' ? static::PENDING : static::COMPLETED;
        }

        $this->transaction->setNote(implode(PHP_EOL, $messages));
        $this->transaction->setDataCell('result', implode(PHP_EOL, $messages), 'Result');

        return $result;
    }

    /**
     * Capture (partially)
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCapturePart(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doCapture($transaction);
    }

    /**
     * Capture (multiple)
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCaptureMulti(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doCapture($transaction);
    }

    /**
     * Capture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $method = $transaction->getPaymentTransaction()->getPaymentMethod();
        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        $api_login_id = $method->getSetting('api_login_id');
        $transaction_key = $method->getSetting('transaction_key');
        $sid = $this->getSetting('mode') == 'test'
            ? 'AAA100302'
            : 'AAA105360';
        $amount = $this->formatCurrency($transaction->getValue());
        $rid = $transaction->getPaymentTransaction()->getDetail('transId');
        $txnid = substr($this->getTransactionId(null, $transaction->getPaymentTransaction()), 0, 20);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication>
        <name>$api_login_id</name>
        <transactionKey>$transaction_key</transactionKey>
    </merchantAuthentication>
    <transactionRequest>
        <transactionType>priorAuthCaptureTransaction</transactionType>
		<amount>$amount</amount>
		<solution>
            <id>$sid</id>
        </solution>
		<refTransId>$rid</refTransId>
        <order>
            <invoiceNumber>$txnid</invoiceNumber>
        </order>
    </transactionRequest>
</createTransactionRequest>
XML;
        $this->log('Capture request: ' . $this->getAPIUrl() . PHP_EOL . $xml);

        $request = new \XLite\Core\HTTP\Request($this->getAPIUrl());
        $request->verb = 'POST';
        $request->body = $xml;
        $response = $request->sendRequest();

        $this->log('Capture response: ' . $response->body);

        $bom = pack('H*','FEFF');
        $rxml = @simplexml_load_string(preg_replace('/^' . $bom . '/', '', $response->body));

        $messages = array();
        foreach ($rxml->transactionResponse->messages->message as $n) {
            $messages[] = (string)$n->description;
        }

        if ((string)$rxml->messages->resultCode == 'Ok' && in_array((string)$rxml->transactionResponse->responseCode, array('1', '4'))) {
            $backendTransactionStatus = $transaction::STATUS_SUCCESS;
        }

        $transaction->setDataCell('transId', (string)$rxml->transactionResponse->transId, 'Transaction ID');
        $transaction->setDataCell('messages', implode('; ', $messages));

        $transaction->setStatus($backendTransactionStatus);

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Void
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $paymentTransaction = $transaction->getPaymentTransaction();
        $method = $transaction->getPaymentTransaction()->getPaymentMethod();
        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        $api_login_id = $method->getSetting('api_login_id');
        $transaction_key = $method->getSetting('transaction_key');
        $sid = $this->getSetting('mode') == 'test'
            ? 'AAA100302'
            : 'AAA105360';
        $rid = $transaction->getPaymentTransaction()->getDetail('transId');

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication>
        <name>$api_login_id</name>
        <transactionKey>$transaction_key</transactionKey>
    </merchantAuthentication>
    <transactionRequest>
        <transactionType>voidTransaction</transactionType>
		<solution>
            <id>$sid</id>
        </solution>
		<refTransId>$rid</refTransId>
    </transactionRequest>
</createTransactionRequest>
XML;
        $this->log('Void request: ' . $this->getAPIUrl() . PHP_EOL . $xml);

        $request = new \XLite\Core\HTTP\Request($this->getAPIUrl());
        $request->verb = 'POST';
        $request->body = $xml;
        $response = $request->sendRequest();

        $this->log('Void response: ' . $response->body);

        $bom = pack('H*','FEFF');
        $rxml = @simplexml_load_string(preg_replace('/^' . $bom . '/', '', $response->body));

        $messages = array();
        foreach ($rxml->transactionResponse->messages->message as $n) {
            $messages[] = (string)$n->description;
        }

        if ((string)$rxml->messages->resultCode == 'Ok' && in_array((string)$rxml->transactionResponse->responseCode, array('1', '4'))) {
            $backendTransactionStatus = $transaction::STATUS_SUCCESS;
            $paymentTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
        }

        $transaction->setDataCell('transId', (string)$rxml->transactionResponse->transId, 'Transaction ID');
        $transaction->setDataCell('messages', implode('; ', $messages));

        $transaction->setStatus($backendTransactionStatus);

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Refund (partially)
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefundPart(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Refund (multiple)
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefundMulti(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $backendTransactionStatus = $transaction::STATUS_FAILED;
        $method = $transaction->getPaymentMethod();

        $api_login_id = $method->getSetting('api_login_id');
        $transaction_key = $method->getSetting('transaction_key');
        $sid = $this->getSetting('mode') == 'test'
            ? 'AAA100302'
            : 'AAA105360';
        $amount = $this->formatCurrency($transaction->getValue());
        $rid = $transaction->getPaymentTransaction()->getDetail('transId');
        $last_4_digits = $transaction->getPaymentTransaction()->getDetail('last_4_digits');
        $txnid = substr($this->getTransactionId(null, $transaction->getPaymentTransaction()), 0, 20);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication>
        <name>$api_login_id</name>
        <transactionKey>$transaction_key</transactionKey>
    </merchantAuthentication>
    <transactionRequest>
        <transactionType>refundTransaction</transactionType>
		<amount>$amount</amount>
		<payment>
			<creditCard>
				<cardNumber>$last_4_digits</cardNumber>
				<expirationDate>XXXX</expirationDate>
			</creditCard>
		</payment>
		<solution>
            <id>$sid</id>
        </solution>
		<refTransId>$rid</refTransId>
		<order>
            <invoiceNumber>$txnid</invoiceNumber>
        </order>
    </transactionRequest>
</createTransactionRequest>
XML;
        $this->log('Refund request: ' . $this->getAPIUrl() . PHP_EOL . $xml);

        $request = new \XLite\Core\HTTP\Request($this->getAPIUrl());
        $request->verb = 'POST';
        $request->body = $xml;
        $response = $request->sendRequest();

        $this->log('Refund response: ' . $response->body);

        $bom = pack('H*','FEFF');
        $rxml = @simplexml_load_string(preg_replace('/^' . $bom . '/', '', $response->body));

        if ((string)$rxml->transactionResponse->responseCode == '3') {
            // Try void
            $rid = $transaction->getPaymentTransaction()->getDetail('transId');

            $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
    <merchantAuthentication>
        <name>$api_login_id</name>
        <transactionKey>$transaction_key</transactionKey>
    </merchantAuthentication>
    <transactionRequest>
        <transactionType>voidTransaction</transactionType>
		<solution>
            <id>$sid</id>
        </solution>
		<refTransId>$rid</refTransId>
    </transactionRequest>
</createTransactionRequest>
XML;

            $this->log('Refund/void request: ' . $this->getAPIUrl() . PHP_EOL . $xml);

            $request = new \XLite\Core\HTTP\Request($this->getAPIUrl());
            $request->verb = 'POST';
            $request->body = $xml;
            $response = $request->sendRequest();

            $this->log('Refund/void response: ' . $response->body);

            $bom = pack('H*','FEFF');
            $rxml = @simplexml_load_string(preg_replace('/^' . $bom . '/', '', $response->body));
        }

        $messages = array();
        foreach ($rxml->transactionResponse->messages->message as $n) {
            $messages[] = (string)$n->description;
        }

        if ((string)$rxml->messages->resultCode == 'Ok' && in_array((string)$rxml->transactionResponse->responseCode, array('1', '4'))) {
            $backendTransactionStatus = $transaction::STATUS_SUCCESS;
        }

        $transaction->setDataCell('transId', (string)$rxml->transactionResponse->transId, 'Transaction ID');
        $transaction->setDataCell('messages', implode('; ', $messages));

        $transaction->setStatus($backendTransactionStatus);
        \XLite\Core\Database::getEM()->flush();

        if (\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus) {

            $order = $transaction->getPaymentTransaction()->getOrder();

            $paymentTransactionSums = $order->getRawPaymentTransactionSums();
            $refunded = $paymentTransactionSums['refunded'];
            $status = $refunded < $transaction->getPaymentTransaction()->getValue()
                ? \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
                : \XLite\Model\Order\Status\Payment::STATUS_REFUNDED;

            $order->setPaymentStatus($status);
            \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been refunded successfully');

        } else {
            $msg = 'Transaction failure.';
            if ($messages) {
                $msg .= ' Server response: ' . implode('; ', $messages);
            }
            \XLite\Core\TopMessage::getInstance()->addError($msg);
        }

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Generate order line items and additional charges
     *
     * @return string
     */
    protected function generateLineItems()
    {
        $result = '';
        if (count($this->getOrder()->getItems()) < 30) {
            $items = array();
            foreach ($this->getOrder()->getItems() as $item) {
                /** @var \XLite\Model\Product $product */
                $product = $item->getProduct();
                $itemId = $this->formatXMLString($item->getSku(), 31);
                $name = $this->formatXMLString($item->getName(), 31);
                $description = $this->formatXMLString($product->getBriefDescription(), 255);
                $quantity = $item->getAmount();
                $unitPrice = $this->formatCurrency($item->getDisplayPrice());

                $items[] = <<<XML
            <lineItem>
                <itemId>$itemId</itemId>
                <name>$name</name>
                <description>$description</description>
                <quantity>$quantity</quantity>
                <unitPrice>$unitPrice</unitPrice>
            </lineItem>
XML;
            }

            if ($items) {
                $result = '<lineItems>' . PHP_EOL
                    . implode(PHP_EOL, $items) . PHP_EOL
                    . '        </lineItems>' . PHP_EOL;

                $taxCost = $this->getOrder()->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
                if ($taxCost) {
                    $taxCost = $this->formatCurrency($taxCost);
                    $result .= <<<XML
        <tax>
            <amount>$taxCost</amount>
        </tax>
XML;
                }

                $dutyCost = $this->getOrder()->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_HANDLING);
                if ($dutyCost) {
                    $dutyCost = $this->formatCurrency($dutyCost);
                    $result .= <<<XML
        <duty>
            <amount>$dutyCost</amount>
        </duty>
XML;
                }

                $shippingCost = $this->getOrder()->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
                if ($shippingCost) {
                    $shippingCost = $this->formatCurrency($shippingCost);
                    $result .= <<<XML
        <shipping>
            <amount>$shippingCost</amount>
        </shipping>
XML;
                }
            }
        }

        return $result;
    }

    /**
     * Generate customer info
     *
     * @return string
     */
    protected function generateCustomer()
    {
        $result = '';

        /** @var \XLite\Model\Profile $profile */
        $profile = $this->getOrder()->getOrigProfile() ?: $this->getOrder()->getProfile();
        if ($profile) {
            $id = $profile->getProfileId();
            $email = $this->formatXMLString($profile->getLogin(), 255);

            $result .= <<<XML
<customer>
            <type>individual</type>
            <id>$id</id>
            <email>$email</email>
        </customer>
XML;

            // Billing address
            $addr = $profile->getBillingAddress();
            if ($addr) {
                $firstName = $this->formatXMLString($addr->getFirstname(), 50);
                $lastName = $this->formatXMLString($addr->getLastname(), 50);
                $address = $this->formatXMLString($addr->getStreet(), 60);
                $city = $this->formatXMLString($addr->getCity(), 40);
                $state = $this->formatXMLString($addr->getStateName(), 40);
                $zip = $this->formatXMLString($addr->getZipcode(), 20);
                $country = $this->formatXMLString($addr->getCountryName(), 60);
                $phoneNumber = $this->formatXMLString(preg_replace('/\D/Ss', '', $addr->getPhone()), 25);

                $result .= <<<XML

        <billTo>
            <firstName>$firstName</firstName>
            <lastName>$lastName</lastName>
            <address>$address</address>
            <city>$city</city>
            <state>$state</state>
            <zip>$zip</zip>
            <country>$country</country>
            <phoneNumber>$phoneNumber</phoneNumber>
        </billTo>
XML;
            }

            // Shipping address
            $addr = $profile->getShippingAddress();
            if ($addr) {
                $firstName = $this->formatXMLString($addr->getFirstname(), 50);
                $lastName = $this->formatXMLString($addr->getLastname(), 50);
                $address = $this->formatXMLString($addr->getStreet(), 60);
                $city = $this->formatXMLString($addr->getCity(), 40);
                $state = $this->formatXMLString($addr->getStateName(), 40);
                $zip = $this->formatXMLString($addr->getZipcode(), 20);
                $country = $this->formatXMLString($addr->getCountryName(), 60);

                $result .= <<<XML

        <shipTo>
            <firstName>$firstName</firstName>
            <lastName>$lastName</lastName>
            <address>$address</address>
            <city>$city</city>
            <state>$state</state>
            <zip>$zip</zip>
            <country>$country</country>
        </shipTo>
XML;
            }
        }

        $result .= PHP_EOL . '        <customerIP>' . $this->getClientIP() . '</customerIP>';

        return $result;
    }

    /**
     * Format XML value
     *
     * @param string  $string String
     * @param integer $length Length limit
     *
     * @return string
     */
    protected function formatXMLString($string, $length)
    {
        $requiredLength = $length;

        do {
            $string = substr($string, 0, $length);
            $result = htmlspecialchars($string, defined('ENT_XML1') ? ENT_XML1 : ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
            if (strlen($result) > $length) {
                $length--;
            }

        } while(strlen($result) > max($length, $requiredLength));

        return $result;
    }

    /**
     * Get API endpoint url
     *
     * @return string
     */
    protected function getAPIUrl($mode = null)
    {
        if (!$mode) {
            $mode = $this->getSetting('mode');
        }

        return $mode == 'test'
            ? 'https://apitest.authorize.net/xml/v1/request.api'
            : 'https://api.authorize.net/xml/v1/request.api';
    }

    /**
     * Format currency 
     * 
     * @param float $value Currency value
     *  
     * @return float
     */
    protected function formatCurrency($value)
    {
        return $this->transaction->getCurrency()->roundValue($value);
    }

    /**
     * Check - transaction is capture type or not
     * 
     * @return boolean
     */
    protected function isCapture()
    {
        return $this->transaction->getType() == \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Register backend transaction 
     * 
     * @param string                           $type        Backend transaction type OPTIONAL
     * @param \XLite\Model\Payment\Transaction $transaction Transaction OPTIONAL
     *  
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerBackendTransaction($type = null, \XLite\Model\Payment\Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = $this->transaction;
        }

        if (!$type) {
            $type = $transaction->getType();
        }

        $backendTransaction = $transaction->createBackendTransaction($type);

        return $backendTransaction;
    }

    /**
     * Log
     *
     * @param string  $message Message
     * @param boolean $force   Force logging OPTIONAL
     */
    protected function log($message, $force = false)
    {
        $logLevel = \XLite::getInstance()->getOptions(['log_details', 'level']);
        if ($force || intval($logLevel) >= LOG_DEBUG) {
            \XLite\Logger::logCustom('AuthorizenetAcceptjs', $message);
        }
    }
}
