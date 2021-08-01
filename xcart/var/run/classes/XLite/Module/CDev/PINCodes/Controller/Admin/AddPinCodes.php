<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

/**
 * PINCodes selected controller
 *
 */
class AddPinCodes extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Add PIN codes');
    }

    /**
     * Add posted codes
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $codes = \XLite\Core\Request::getInstance()->codes;

        $codes = array_filter(array_map('trim', explode("\n", $codes)));

        $this->addPinCodes($codes);

        if ($product = $this->getProduct()) {
            \XLite\Core\Event::remainingPinCodes([
                'count' => $product->getRemainingPinCodesCount()
            ]);
        }
    }

    /**
     * Add codes from csv file
     *
     * @return void
     */
    protected function doActionImport()
    {
        if ($this->checkFileExtension(\XLite\Core\Session::getInstance()->pinCodesImportFile)) {
            $stream = fopen(\XLite\Core\Session::getInstance()->pinCodesImportFile, 'rb');
            $this->addFromStreamAction($stream);
            if ($stream) {
                fclose($stream);
            }
        } else {
            \XLite\Logger::getInstance()->log(
                'Uploaded file (' . \XLite\Core\Session::getInstance()->pinCodesImportFile . ') must be of CSV format'
                . ' Request data: ' . print_r(\XLite\Core\Request::getInstance()->getData(), true),
                LOG_ERR
            );
            \XLite\Core\TopMessage::addError('Only CSV files can be imported');
        }

        $this->setReturnUrl(
            $this->buildUrl(
                'product',
                '',
                ['product_id' => \XLite\Core\Request::getInstance()->product_id, 'page' => 'pin_codes']
            )
        );
    }

    /**
     * @param $path
     * @return string
     */
    protected function checkFileExtension($path)
    {
        return $this->fileExtensionIs('.csv', $path);
    }

    /**
     * @param $ext
     * @param $path
     * @return bool
     */
    protected function fileExtensionIs($ext, $path)
    {
        $length = strlen($ext);

        return $length === 0 || substr($path, -$length) === $ext;
    }

    /**
     * @return \XLite\Model\Product|null
     */
    protected function getProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find(
            \XLite\Core\Request::getInstance()->product_id
        );
    }

    /**
     * Set sale price parameters for products list
     *
     * @param array $pinCodes
     */
    protected function addPinCodes($pinCodes)
    {
        $product = $this->getProduct();

        if (!$product) {
            \XLite\Core\TopMessage::addError('Product not found');

        } elseif (!empty($pinCodes) && is_array($pinCodes)) {
            $count = count($pinCodes);
            $pinCodes = array_unique($pinCodes);

            $created = 0;
            $duplicates = $count - count($pinCodes);
            $exceededLength = 0;
            $maxLength = 64;

            $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode');
            $i = 0;

            foreach ($pinCodes as $code) {
                if (strlen($code) > $maxLength) {
                    $exceededLength++;
                    continue;
                }

                if (!$repo->findOneBy([
                    'product' => $product,
                    'code'    => $code,
                ])) {
                    $object = $repo->insert(null, false);
                    $object->setCode($code);
                    $object->setProduct($product);
                    $created++;
                    $i++;
                } else {
                    $duplicates++;
                }

                if ($i > 1000) {
                    \XLite\Core\Database::getEM()->flush();
                    $i = 0;
                }
            }

            $product->changeAmount($created);
            \XLite\Core\Database::getEM()->flush();

            if ($created) {
                \XLite\Core\TopMessage::addInfo(
                    static::t('X PIN codes created successfully.', ['count' => $created])
                );
            }
            if ($duplicates) {
                \XLite\Core\TopMessage::addWarning(
                    static::t('X PIN code duplicates ignored.', ['count' => $duplicates])
                );
            }
            if ($exceededLength) {
                \XLite\Core\TopMessage::addError(
                    static::t(
                        'X PIN codes longer than Y characters ignored.',
                        ['count' => $exceededLength, 'max' => $maxLength]
                    )
                );
            }
        }

        if ($product && empty($created) && empty($duplicates) && empty($exceededLength)) {
            \XLite\Core\TopMessage::addError(static::t('No valid code found.'));
        }
    }

    /**
     * Set sale price parameters for products list
     *
     * @param resource $stream Stream
     *
     * @return void
     */
    protected function addFromStreamAction($stream)
    {
        $product = $this->getProduct();

        if (!is_resource($stream)) {
            \XLite\Logger::getInstance()->log(
                'No valid resource supplied to add pin codes controller.'
                . ' Data type: ' . gettype($stream),
                LOG_ERR
            );
            \XLite\Core\TopMessage::addError('Unknown error occurred');

        } elseif (!$product) {
            \XLite\Logger::getInstance()->log(
                'No valid product id supplied to add pin codes controller.'
                . ' Request data: ' . print_r(\XLite\Core\Request::getInstance()->getData(), true),
                LOG_ERR
            );
            \XLite\Core\TopMessage::addError('Product not found');

        } elseif (!$product) {
            \XLite\Logger::getInstance()->log(
                'No valid product id supplied to add pin codes controller.'
                . ' Request data: ' . print_r(\XLite\Core\Request::getInstance()->getData(), true),
                LOG_ERR
            );
            \XLite\Core\TopMessage::addError('Product not found');

        } else {
            $codes = [];
            $created = 0;
            $duplicates = 0;
            $exceededLength = 0;
            $maxLength = 64;

            for ($data = fgetcsv($stream); false !== $data; $data = fgetcsv($stream)) {
                $code = trim($data[0]);

                if (strlen($code) > $maxLength) {
                    $exceededLength++;
                    $code = '';
                }

                if (!empty($code)) {
                    $existing = \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')->findOneBy(
                        [
                            'product' => $product->getId(),
                            'code' => $code
                        ]
                    );
                    if (!$existing) {
                        $existing = in_array($code, $codes);
                    }
                    if ($existing) {
                        $duplicates++;
                    } else {
                        $object = new \XLite\Module\CDev\PINCodes\Model\PinCode;
                        $object->setCode($code);
                        $object->setProduct($product);
                        \XLite\Core\Database::getEM()->persist($object);
                        $created++;
                    }

                    $codes[] = $code;

                    if (1000 < count($codes)) {
                        \XLite\Core\Database::getEM()->flush();
                        $codes = [];
                    }
                }
            }

            $product->changeAmount($created);
            \XLite\Core\Database::getEM()->flush();

            if ($created) {
                \XLite\Core\TopMessage::addInfo(
                    static::t('X PIN codes created successfully.', ['count' => $created])
                );
            }
            if ($duplicates) {
                \XLite\Core\TopMessage::addWarning(
                    static::t('X PIN code duplicates ignored.', ['count' => $duplicates])
                );
            }
            if ($exceededLength) {
                \XLite\Core\TopMessage::addError(
                    static::t(
                        'X PIN codes longer than Y characters ignored.',
                        ['count' => $exceededLength, 'max' => $maxLength]
                    )
                );
            }
            if (!$created && !$duplicates && !$exceededLength) {
                \XLite\Core\TopMessage::addError(static::t('No valid code found.'));
            }
        }

    }
}
