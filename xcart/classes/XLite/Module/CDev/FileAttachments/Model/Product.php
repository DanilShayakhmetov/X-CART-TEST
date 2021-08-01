<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model;

use \XLite\Module\CDev\FileAttachments\Model\Product\Attachment;

/**
 * Product 
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product attachments
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"orderby" = "ASC"})
     */
    protected $attachments;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        foreach ($this->getAttachments() as $attachment) {
            $attachment->cloneEntityForProduct($newProduct);
        }
    
        $newProduct->update(true);

        return $newProduct;
    }

    /**
     * Add attachments
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachments
     * @return Product
     */
    public function addAttachments(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachments)
    {
        $this->attachments[] = $attachments;
        return $this;
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Return filtered attachments
     *
     * @param \XLite\Model\Profile $profile OPTIONAL
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilteredAttachments($profile = null)
    {
        return $this->getAttachments()->filter($this->getAttachmentsFilter($profile));
    }

    /**
     * Returns image comparing closure
     *
     * @param \XLite\Model\Profile $profile OPTIONAL
     *
     * @return \Closure
     */
    protected function getAttachmentsFilter($profile = null)
    {
        /**
         * @param Attachment $element
         *
         * @return boolean
         */
        return function ($element) use ($profile) {
            if ($element->getAccess() === Attachment::ACCESS_ANY) {
                return true;
            } elseif ($element->getAccess() === Attachment::ACCESS_REGISTERED) {
                return null !== $profile;
            }

            $membershipId = ($profile && $profile->getMembership())
                ? $profile->getMembership()->getMembershipId()
                : null;

            return (integer)$element->getAccess() === $membershipId;
        };
    }
}
