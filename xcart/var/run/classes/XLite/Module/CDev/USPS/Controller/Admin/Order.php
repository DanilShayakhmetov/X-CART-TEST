<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Controller\Admin;

use XLite\Module\CDev\USPS\Main;
use XLite\Module\CDev\USPS\Model\Shipment;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\RequestException;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\FactoryException;
use XLite\Module\CDev\USPS\View\Model\CreateShipment;

 class Order extends \XLite\Module\CDev\VolumeDiscounts\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_USPS_PB_SHIPMENTS = 'usps_pb_shipments';

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if ($this->isUSPSPBShippingMethod()) {
            $list[static::PAGE_USPS_PB_SHIPMENTS] = static::t('USPS Shipments');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        if ($this->isUSPSPBShippingMethod()) {
            $list[static::PAGE_USPS_PB_SHIPMENTS] = 'modules/CDev/USPS/shipments/page.twig';
        }

        return $list;
    }

    /**
     * @return boolean
     */
    protected function isUSPSPBShippingMethod()
    {
        $order    = $this->getOrder();
        $modifier = $order ? $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING') : null;
        $method   = $modifier ? $modifier->getMethod() : null;

        return $method && $method->getCarrier() === 'pb_usps' && $this->isLocalShipping($order);
    }

    protected function doActionUspsCreateShipment()
    {
        $shipment = new Shipment();

        $form = new CreateShipment();
        $form->saveCurrentFormData();

        $requestData = $this->getShipmentDataFromRequest();
        $shipment->setRequestData($requestData);

        try {
            $createShipmentRequest = Main::getRequestFactory()
                ->createCreateShipmentRequest($shipment->getTransactionId(), $requestData);
            $response              = $createShipmentRequest->performRequest();

            $shipment->setOrder($this->getOrder());
            $shipment->setResponseData($response);
            $shipment->setPrintDate(\LC_START_TIME);
            $shipment->setPrice($response['rates'][0]['totalCarrierCharge']);
            $shipment->setShipmentId($response['shipmentId']);
            $shipment->setTrackingNumber($response['parcelTrackingNumber']);

            $document = $response['documents'][0];
            if (isset($document['contents'])) {
                $shipment->setLabelURL($document['contents']);
            }
            if (isset($document['pages'])) {
                $shipment->setLabelContent($document['pages']);
            }

            $this->addTrackingNumber($response['parcelTrackingNumber']);

            \XLite\Core\Database::getEM()->persist($shipment);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('Shipment has been created successfully');

        } catch (FactoryException $e) {
            \XLite\Core\TopMessage::addWarning($e->getMessage());

        } catch (RequestException $e) {
            \XLite\Core\TopMessage::addWarning($e->getMessage());
        }
    }

    /**
     * @return array
     */
    protected function getShipmentDataFromRequest()
    {
        $order   = $this->getOrder();
        $profile = $order->getProfile();
        /** @var \XLite\Model\Shipping\Method $method */
        $method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($order->getShippingId());

        $request = \XLite\Core\Request::getInstance();

        $config     = \XLite\Core\Config::getInstance();
        $uspsConfig = $config->CDev->USPS;

        $sourceAddress = $order->getSourceAddress();
        $fromAddress = Helper::convertArrayAddressToPBAddress($sourceAddress->toArray());
        if (
            'pb_usps' === $method->getCarrier()
            && 'EM' === $method->getCode()
        ) {
            $fromAddress['phone'] = $sourceAddress->getPhone();
        }

        $toAddress   = Helper::convertArrayAddressToPBAddress($profile->getShippingAddress()->toArray());
        $packageType = $toAddress['countryCode'] === 'US'
            ? $uspsConfig->pb_domestic_parcel_type
            : $uspsConfig->pb_international_parcel_type;

        $labelFormat = $request->labelFormat;

        $result = [
            'fromAddress'     => $fromAddress,
            'toAddress'       => $toAddress,
            'parcel'          => [
                'weight'    => [
                    'weight'            => Helper::toOunces($request->weight, $config->Units->weight_unit),
                    'unitOfMeasurement' => 'OZ',
                ],
                'dimension' => [
                    'length'               => $request->dimensions[0],
                    'width'                => $request->dimensions[1],
                    'height'               => $request->dimensions[2],
                    'unitOfMeasurement'    => 'IN',
                    'irregularParcelGirth' => $request->girth,
                ],
            ],
            'rates'           => [],
            'documents'       => [
                [
                    'type'              => 'SHIPPING_LABEL',
                    'contentType'       => $labelFormat === 'PDF' ? 'URL' : 'BASE64',
                    'size'              => $labelFormat === 'ZPL2' ? 'DOC_4X6' : $request->labelSize,
                    'fileFormat'        => $labelFormat,
                    'printDialogOption' => $labelFormat === 'PDF' ? 'EMBED_PRINT_DIALOG' : 'NO_PRINT_DIALOG',
                ],
            ],
            'shipmentOptions' => [
                [
                    'name'  => 'SHIPPER_ID',
                    'value' => $uspsConfig->pbShipperId,
                ],
                [
                    'name'  => 'MINIMAL_ADDRESS_VALIDATION',
                    'value' => true,
                ],
            ],
        ];

        $rate = [
            'carrier'             => 'USPS',
            'serviceId'           => $method->getCode(),
            'inductionPostalCode' => $fromAddress['postalCode'],
            'parcelType'          => $packageType,
            'specialServices'     => [],
        ];

        foreach ($request->specialServices as $serviceId) {
            $service = ['specialServiceId' => $serviceId];
            if ($serviceId === 'Ins') {
                $service['inputParameters'] = [
                    [
                        'name'  => 'INPUT_VALUE',
                        'value' => $request->insuranceValue,
                    ],
                ];
            }

            if ($serviceId === 'InsRD') {
                $service['inputParameters'] = [
                    [
                        'name'  => 'INPUT_VALUE',
                        'value' => $request->insuranceValue,
                    ],
                ];
            }

            if ($serviceId === 'COD') {
                $service['inputParameters'] = [
                    [
                        'name'  => 'INPUT_VALUE',
                        'value' => $request->CODValue,
                    ],
                ];
            }

            $rate['specialServices'][] = $service;
        }

        $result['rates'] = [$rate];

        if ($request->hideTotalCarrierCharge) {
            $result['shipmentOptions'][] = [
                'name'  => 'HIDE_TOTAL_CARRIER_CHARGE',
                'value' => true,
            ];
        }

        if ($request->nonDeliveryOption) {
            $result['shipmentOptions'][] = [
                'name'  => 'NON_DELIVERY_OPTION',
                'value' => $request->nonDeliveryOption,
            ];
        }

        if ($request->printCustomMessage1) {
            $result['shipmentOptions'][] = [
                'name'  => 'PRINT_CUSTOM_MESSAGE_1',
                'value' => $request->printCustomMessage1,
            ];
        }

        if ($request->printCustomMessage2) {
            $result['shipmentOptions'][] = [
                'name'  => 'PRINT_CUSTOM_MESSAGE_2',
                'value' => $request->printCustomMessage2,
            ];
        }

        if ($request->shippingLabelSenderSignature) {
            $result['shipmentOptions'][] = [
                'name'  => 'SHIPPING_LABEL_SENDER_SIGNATURE',
                'value' => $request->shippingLabelSenderSignature,
            ];
        }

        return $result;
    }

    protected function doActionUspsUpdateTracking()
    {
        $shipmentId = \XLite\Core\Request::getInstance()->id;
        /** @var \XLite\Module\CDev\USPS\Model\Shipment $shipment */
        $shipment = \XLite\Core\Database::getRepo('XLite\Module\CDev\USPS\Model\Shipment')->find($shipmentId);

        if ($shipment) {
            try {
                $trackingRequest = Main::getRequestFactory()->createTrackingRequest($shipment->getTrackingNumber());
                $trackingData    = $trackingRequest->performRequest();

                $shipment->setTrackingData($trackingData);
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\TopMessage::addInfo('Tracking information was updated successfully');

            } catch (FactoryException $e) {
                \XLite\Core\TopMessage::addWarning($e->getMessage());

            } catch (RequestException $e) {
                \XLite\Core\TopMessage::addWarning($e->getMessage());
            }
        }
    }

    protected function doActionUspsGetLabel()
    {
        $shipmentId = \XLite\Core\Request::getInstance()->id;
        /** @var \XLite\Module\CDev\USPS\Model\Shipment $shipment */
        $shipment = \XLite\Core\Database::getRepo('XLite\Module\CDev\USPS\Model\Shipment')->find($shipmentId);

        if ($shipment && $shipment->getLabelContent()) {
            $labels = $shipment->getLabelContent();
            $index  = \XLite\Core\Request::getInstance()->index;

            if (isset($labels[$index]['contents'])) {
                $response = $shipment->getResponseData();
                $document = $response['documents'][0];
                $format   = $document['fileFormat'];

                $filename = ($shipment->getTrackingNumber() ?: $shipment->getShipmentId()) . '_' . ($index + 1);

                if ($format === 'PNG') {
                    header('Content-Type: image/png');
                } elseif ($format === 'ZPL2') {
                    header('Content-Type: application/unknown');
                    header('Content-Disposition: attachment; filename=' . $filename . '.zplii');
                }

                echo base64_decode($labels[$index]['contents']);
            }
        }

        exit;
    }

    protected function doActionUspsVoidShipment()
    {
        $shipmentId = \XLite\Core\Request::getInstance()->id;
        /** @var \XLite\Module\CDev\USPS\Model\Shipment $shipment */
        $shipment = \XLite\Core\Database::getRepo('XLite\Module\CDev\USPS\Model\Shipment')->find($shipmentId);

        if ($shipment) {
            try {
                $deleteShipmentRequest = Main::getRequestFactory()->createDeleteShipmentRequest(
                    $shipment->getTransactionId(),
                    $shipment->getShipmentId()
                );
                $response = $deleteShipmentRequest->performRequest();

                if ($response && $response['status'] === 'INITIATED') {
                    \XLite\Core\TopMessage::addInfo('Shipment has been voided successfully');

                    $this->removeTrackingNumber($shipment->getTrackingNumber());

                    \XLite\Core\Database::getEM()->remove($shipment);
                    \XLite\Core\Database::getEM()->flush();
                }
            } catch (FactoryException $e) {
                \XLite\Core\TopMessage::addWarning($e->getMessage());

            } catch (RequestException $e) {
                \XLite\Core\TopMessage::addWarning($e->getMessage());
            }
        }
    }

    /**
     * @param string $value
     *
     * @return \XLite\Model\OrderTrackingNumber
     */
    protected function getTrackingNumberByValue($value)
    {
        foreach ($this->getOrder()->getTrackingNumbers() as $trackingNumber) {
            if ($trackingNumber->getValue() === $value) {

                return $trackingNumber;
            }
        }

        return null;
    }

    /**
     * @param string $value
     */
    protected function addTrackingNumber($value)
    {
        $trackingNumber = $this->getTrackingNumberByValue($value);

        if ($trackingNumber === null) {
            $trackingNumber = new \XLite\Model\OrderTrackingNumber();
            $trackingNumber->setOrder($this->getOrder());
            $trackingNumber->setValue($value);

            \XLite\Core\Database::getEM()->persist($trackingNumber);
        }
    }

    /**
     * @param string $value
     */
    protected function removeTrackingNumber($value)
    {
        $trackingNumber = $this->getTrackingNumberByValue($value);

        if ($trackingNumber !== null) {
            $this->getOrder()->getTrackingNumbers()->removeElement($trackingNumber);
            \XLite\Core\Database::getEM()->remove($trackingNumber);
        }
    }

    /**
     * Check the shipping inside the country
     *
     * @param \XLite\Model\Order $order
     * @return bool
     */
    protected function isLocalShipping(\XLite\Model\Order $order)
    {
        $profile = $order->getProfile();

        $dstAddr = $profile->getShippingAddress()->getCountryCode();
        $srcAddr = $order->getSourceAddress()->getCountryCode();

        return $dstAddr === $srcAddr;
    }
}
