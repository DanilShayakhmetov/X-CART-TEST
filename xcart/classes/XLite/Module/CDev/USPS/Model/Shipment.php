<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model;

use XLite\Core\Converter;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;

/**
 * PitneyBowes shipment
 *
 * @Entity
 * @Table  (name="usps_shipment")
 */
class Shipment extends \XLite\Model\AEntity
{
    const TRANSACTION_ID_LENGTH = 25;

    protected static $chars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z',
    ];

    /**
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var string
     *
     * @Column (type="string")
     */
    protected $transactionId;

    /**
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="items")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * @var array
     *
     * @Column (type="array")
     */
    protected $requestData = [];

    /**
     * @var array
     *
     * @Column (type="array")
     */
    protected $responseData = [];

    /**
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $printDate;

    /**
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $price;

    /**
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $shipmentId;

    /**
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $trackingNumber;

    /**
     * @var array
     *
     * @Column (type="array")
     */
    protected $trackingData = [];

    /**
     * @var string
     *
     * @Column (type="string")
     */
    protected $labelURL = '';

    /**
     * @var array
     *
     * @Column (type="array")
     */
    protected $labelContent = [];

    public function __construct(array $data = [])
    {
        $this->transactionId = static::generateTransactionId();

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public static function generateTransactionId()
    {
        return \XLite\Core\Operator::getInstance()->generateToken(static::TRANSACTION_ID_LENGTH, static::$chars);
    }

    /**
     * @return array
     */
    public function getFees()
    {
        $response = $this->getResponseData();
        $rate     = $response['rates'][0];

        $services = Helper::getSpecialServices();

        $result = [];
        foreach ($rate['specialServices'] as $service) {
            $name     = isset($services[$service['specialServiceId']])
                ? $services[$service['specialServiceId']]['name']
                : $service['specialServiceId'];
            $result[] = [
                'name'  => $name,
                'value' => $service['fee'],
            ];
        }

        return $result;
    }

    public function getShippingMethodName()
    {
        $response  = $this->getResponseData();
        $rate      = $response['rates'][0];
        $serviceId = $rate['serviceId'];

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
        /** @var \XLite\Model\Shipping\Method $method */
        $method = $repo->findOneBy(['carrier' => 'pb_usps', 'code' => $serviceId]);

        return $method ? $method->getName() : $serviceId;
    }

    public function getParcelType()
    {
        $response   = $this->getResponseData();
        $rate       = $response['rates'][0];
        $parcelType = $rate['parcelType'];

        $parcelTypes = Helper::getParcelTypes();

        return $parcelTypes[$parcelType];
    }

    public function getWeight($units = null)
    {
        $response = $this->getResponseData();
        $parcel   = $response['parcel'];
        $weight   = $parcel['weight'];

        return $units
            ? Converter::convertWeightUnits($weight['weight'], strtolower($weight['unitOfMeasurement']), $units)
            : $weight['weight'];
    }

    public function getLength($units = null)
    {
        return $this->getDimension('length', $units);
    }

    public function getWidth($units = null)
    {
        return $this->getDimension('width', $units);
    }

    public function getHeight($units = null)
    {
        return $this->getDimension('height', $units);
    }

    protected function getDimension($dimensionType, $units = null)
    {
        $response  = $this->getResponseData();
        $parcel    = $response['parcel'];
        $dimension = $parcel['dimension'];
        $value     = $dimension[$dimensionType];

        return $units
            ? Converter::convertDimensionUnits($value, strtolower($dimension['unitOfMeasurement']), $units)
            : $value;
    }

    public function getGirth()
    {
        $response  = $this->getResponseData();
        $parcel    = $response['parcel'];
        $dimension = $parcel['dimension'];

        return $dimension['irregularParcelGirth'];
    }

    public function getTrackingStatus()
    {
        $trackingData = $this->getTrackingData();

        return $trackingData && $trackingData['status']
            ? $trackingData['status']
            : '';
    }

    public function getTrackingUpdateDate()
    {
        $trackingData = $this->getTrackingData();

        return $trackingData && $trackingData['updatedDate'] && $trackingData['updatedTime']
            ? $trackingData['updatedDate'] . ' ' . $trackingData['updatedTime']
            : '';
    }

    public function getTrackingShipDate()
    {
        $trackingData = $this->getTrackingData();

        return $trackingData && $trackingData['shipDate']
            ? $trackingData['shipDate']
            : '';
    }

    public function getTrackingEstimatedDeliveryDate()
    {
        $trackingData = $this->getTrackingData();

        return $trackingData && $trackingData['estimatedDeliveryDate'] && $trackingData['estimatedDeliveryTime']
            ? $trackingData['estimatedDeliveryDate'] . ' ' . $trackingData['estimatedDeliveryTime']
            : '';
    }

    public function getTrackingDeliveryDate()
    {
        $trackingData = $this->getTrackingData();

        return $trackingData && $trackingData['deliveryDate'] && $trackingData['deliveryTime']
            ? $trackingData['deliveryDate'] . ' ' . $trackingData['deliveryTime']
            : '';
    }

    /**
     * @return boolean
     */
    public function isVoidAvailable()
    {
        return $this->getPrintDate() + 2592000 > \LC_START_TIME;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param \XLite\Model\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @param array $requestData
     */
    public function setRequestData($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * @return array
     */
    public function getResponseData()
    {
        return $this->responseData;
    }

    /**
     * @param array $responseData
     */
    public function setResponseData($responseData)
    {
        $this->responseData = $responseData;
    }

    /**
     * @return int
     */
    public function getPrintDate()
    {
        return $this->printDate;
    }

    /**
     * @param int $printDate
     */
    public function setPrintDate($printDate)
    {
        $this->printDate = $printDate;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getShipmentId()
    {
        return $this->shipmentId;
    }

    /**
     * @param string $shipmentId
     */
    public function setShipmentId($shipmentId)
    {
        $this->shipmentId = $shipmentId;
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @return array
     */
    public function getTrackingData()
    {
        return $this->trackingData;
    }

    /**
     * @param array $trackingData
     */
    public function setTrackingData($trackingData)
    {
        $this->trackingData = $trackingData;
    }

    /**
     * @return string
     */
    public function getLabelURL()
    {
        return $this->labelURL;
    }

    /**
     * @param string $labelURL
     */
    public function setLabelURL($labelURL)
    {
        $this->labelURL = $labelURL;
    }

    /**
     * @return array
     */
    public function getLabelContent()
    {
        return $this->labelContent;
    }

    /**
     * @param array $labelContent
     */
    public function setLabelContent($labelContent)
    {
        $this->labelContent = $labelContent;
    }
}
