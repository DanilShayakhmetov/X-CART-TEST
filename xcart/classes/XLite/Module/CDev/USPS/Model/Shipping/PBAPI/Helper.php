<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI;

class Helper
{
    public static function convertArrayAddressToPBAddress($address)
    {
        $result = [];
        foreach ($address as $field => $value) {
            switch ($field) {
                case 'name':
                    $result['name'] = $value;
                    break;
                case 'address':
                    $result['addressLines'] = [$value];
                    break;
                case 'city':
                    $result['cityTown'] = $value;
                    break;
                case 'state':
                    $result['stateProvince'] = $value;
                    break;
                case 'zipcode':
                    $result['postalCode'] = $value;
                    break;
                case 'country':
                    $result['countryCode'] = $value;
                    break;
                case 'type':
                    $result['residential'] = $value === \XLite\View\FormField\Select\AddressType::TYPE_RESIDENTIAL;
                    break;
            }
        }

        return $result;
    }

    public static function toOunces($weight, $unit)
    {
        switch ($unit) {
            case 'lbs':
                $ounces = 16 * $weight;
                break;

            case 'oz':
                $ounces = $weight;
                break;

            default:
                $ounces = \XLite\Core\Converter::convertWeightUnits(
                    $weight,
                    $unit,
                    'oz'
                );
        }

        return $ounces;
    }

    public static function getSpecialServices()
    {
        return [
            'Ins'               => [
                'name'      => 'Insured Mail',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB', 'FCMI', 'FCPIS', 'PMI', 'EMI'],
            ],
            'RR'                => [
                'name'      => 'Return Receipt',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'Sig'               => [
                'name'      => 'Signature Required',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'Cert'              => [
                'name'      => 'Certified Mail',
                'serviceId' => ['FCM', 'PM'],
            ],
            'DelCon'            => [
                'name'      => 'Delivery Confirmation',
                'serviceId' => ['FCM', 'PM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'ERR'               => [
                'name'      => 'Electronic Return Receipt',
                'serviceId' => ['FCM', 'PM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'RRM'               => [
                'name'      => 'Return Receipt for Merchandise',
                'serviceId' => ['FCM', 'PM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'Reg'               => [
                'name'      => 'Registered Mail',
                'serviceId' => ['FCM', 'PM'],
            ],
            'RegIns'            => [
                'name'      => 'Registered Mail with Insurance',
                'serviceId' => ['FCM'],
            ],
            'SH'                => [
                'name'      => 'Special Handling - Fragile',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'CertRD'            => [
                'name'      => 'Certified Mail with Restricted Delivery',
                'serviceId' => ['FCM', 'PM'],
            ],
            'COD'               => [
                'name'      => 'Collect On Delivery (COD)',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'CODRD'             => [
                'name'      => 'Collect On Delivery with Restricted Delivery',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'InsRD'             => [
                'name'      => 'Insured Mail with Restricted Delivery',
                'serviceId' => ['FCM', 'PM', 'EM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'RegRD'             => [
                'name'      => 'Registered with Restricted Delivery',
                'serviceId' => ['FCM', 'PM'],
            ],
            'RegCOD'            => [
                'name'      => 'Registered with COD',
                'serviceId' => ['FCM', 'PM'],
            ],
            'SigRD'             => [
                'name'      => 'Signature with Restricted Delivery',
                'serviceId' => ['FCM', 'PM', 'MEDIA', 'PRCLSEL', 'STDPOST', 'LIB'],
            ],
            'RegInsRD'          => [
                'name'      => 'Registered with Insurance and Restricted Delivery',
                'serviceId' => ['FCM', 'PM'],
            ],
            'hazmat'            => [
                'name'      => 'Hazardous Materials',
                'serviceId' => ['FCM', 'PM', 'EM', 'PRCLSEL', 'STDPOST'],
            ],
            'liveanimal'        => [
                'name'      => 'Live Animal Surcharge',
                'serviceId' => ['FCM', 'PM', 'EM', 'PRCLSEL', 'STDPOST'],
            ],
            'liveanimalpoultry' => [
                'name'      => 'Live Animal-Day Old Poultry Surcharge',
                'serviceId' => ['FCM', 'PM', 'EM', 'PRCLSEL', 'STDPOST'],
            ],
            'holiday'           => [
                'name'      => 'Holiday Delivery- For Priority Mail Express Service Only',
                'serviceId' => ['EM'],
            ],
            'Sunday'            => [
                'name'      => 'Sunday Delivery',
                'serviceId' => ['EM'],
            ],
            'Sunday-holiday'    => [
                'name'      => 'Sunday holiday Delivery',
                'serviceId' => ['EM'],
            ],
        ];
    }

    public static function getParcelTypes()
    {
        return [
            'FRB'      => 'Flat Rate Box',
            'FRE'      => 'Flat Rate Envelope',
            'LGENV'    => 'Large Envelope',
            'LFRB'     => 'Large Flat Rate Box',
            'LGLFRENV' => 'Legal Flat Rate Envelope',
            'MLFRB'    => 'Military Large Flat Rate Box',
            'PKG'      => 'Package',
            'PFRENV'   => 'Padded Flat Rate Envelope',
            'RBA'      => 'Regional Box A',
            'RBB'      => 'Regional Box B',
            'SFRB'     => 'Small Flat Rate Box',
            'LTR'      => 'Letter',
        ];
    }
}
