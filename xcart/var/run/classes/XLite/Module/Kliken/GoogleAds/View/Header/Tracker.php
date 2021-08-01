<?php

namespace XLite\Module\Kliken\GoogleAds\View\Header;

/**
 * Header declaration
 *
 * @ListChild (list="head")
 */
class Tracker extends \XLite\View\AView
{
    private $accountNumber = null;

    protected function getDefaultTemplate()
    {
        return 'modules/Kliken/GoogleAds/tracking-script.twig';
    }

    /**
     * Return account number to be used in tracking script
     *
     * @return int
     */
    protected function getAccountNumber()
    {
        if ($this->accountNumber !== null) return intval($this->accountNumber);

        $this->accountNumber = intval(\XLite\Core\Config::getInstance()->Kliken->GoogleAds->account_id);

        return $this->accountNumber;
    }

    protected function getCurrentProductDetails()
    {
        $controller = \XLite::getController();

        if ($controller instanceof \XLite\Controller\Customer\Product) {
            $item = $this->getProduct();

            if ($item === null) return null;

            $productInfo = [
                'id'       => $item->getId(),
                'name'     => $item->getName(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getQty(),
                'category' => $item->getCategoryId()
            ];
            return $productInfo;
        }

        return null;
    }

    protected function getCurrentCartDetails()
    {
        $controller = \XLite::getController();

        if ($controller instanceof \XLite\Controller\Customer\Cart) {
            $cart = $this->getCart();

            if ($cart == null) return null;

            return $cart;
        }

        return null;
	}

    protected function getOrderDetails()
    {
        $controller = \XLite::getController();

        if ($controller instanceof \XLite\Controller\Customer\CheckoutSuccess) {
            $order = $this->getOrder();

            if (!$order->getProfile()) return null;

            $bAddress = $order->getProfile()->getBillingAddress();

            $trans = [
                'order_id'  => $order->getOrderNumber(),
                'affiliate' => null,
                'sub_total' => $order->getSubtotal(),
                'tax'       => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX),
                'city'      => $bAddress ? $bAddress->getCity() : '',
                'state'     => ($bAddress && $bAddress->getState()) ? $bAddress->getState()->getState() : '',
                'country'   => ($bAddress && $bAddress->getCountry()) ? $bAddress->getCountry()->getCountry() : '',
                'total'     => $order->getTotal(),
                'currency'  => $order->getCurrency()->getCode(),
                'items'     => []
            ];

            foreach ($order->getItems() as $item) {
                $orderItem = [
                    'id'       => $item->getProductId(),
                    'name'     => $item->getName(),
                    'price'    => $item->getPrice(),
                    'quantity' => $item->getAmount()
                ];

                // Get a random category assigned to the product
                $category = $item->getProduct() ? $item->getProduct()->getCategory() : null;
                if ($category) {
                    foreach ($category->getPath() as $catPath) {
                        $orderItem['category'] = $catPath->getName();
                    }
                }

                $trans['items'][] = $orderItem;
            }

            return $trans;
        }

        return null;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->hasAccountNumber();
    }

    private function hasAccountNumber()
    {
        return $this->getAccountNumber() > 0;
    }
}
