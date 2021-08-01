<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model;

/**
 * The "review" model class
 *
 * @Entity
 * @Table  (name="reviews",
 *      indexes={
 *          @Index (name="additionDate", columns={"additionDate"}),
 *          @Index (name="status", columns={"status"}),
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Review extends \XLite\Model\AEntity
{
    const STATUS_APPROVED               = 1;
    const STATUS_PENDING                = 0;
    const MAX_RATING                    = 5;
    const REGISTERED_CUSTOMERS          = 'R';
    const PURCHASED_CUSTOMERS           = 'P';

    /**
     * Review Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Review text
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $review = '';

    /**
     * Response text
     *
     * @var string
     *
     * @Column (type="text", nullable=true)
     */
    protected $response = '';

    /**
     * Review rating
     *
     * @var integer
     *
     * @Column (type="smallint")
     */
    protected $rating = self::MAX_RATING;

    /**
     * Addition date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $additionDate;

    /**
     * Respond date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true)
     */
    protected $responseDate;

    /**
     * Relation to a profile entity (who adds review)
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="reviews")
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $profile;

    /**
     * Respondent profile
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile")
     * @JoinColumn (name="respondent_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $respondent;

    /**
     * Reviewer name
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $reviewerName = '';

    /**
     * Review status
     *
     * @var integer
     *
     * @Column (type="smallint")
     */
    protected $status = self::STATUS_PENDING;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="reviews")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Use for meta flag
     *
     * @var boolean
     *
     * @Column(type="boolean")
     */
    protected $useForMeta = false;

    /**
     * Flag: New review (flag has reset after admin view the review in the list)
     *
     * @var boolean
     *
     * @Column(type="boolean")
     */
    protected $isNew = true;

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xcPendingExport = false;

    /**
     * Related order review key
     *
     * @var \XLite\Module\XC\Reviews\Model\OrderReviewKey
     *
     * @ManyToOne (targetEntity="XLite\Module\XC\Reviews\Model\OrderReviewKey", inversedBy="review", fetch="LAZY")
     * @JoinColumn (name="rkey_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reviewKey;

    /**
     * Define if review is new
     *
     * @return boolean
     */
    public function isNew()
    {
        return !$this->isPersistent();
    }

    /**
     * Define if review is approved
     *
     * @return boolean
     */
    public function isApproved()
    {
        return $this->getStatus() == static::STATUS_APPROVED;
    }

    /**
     * Define if review is not approved
     *
     * @return boolean
     */
    public function isNotApproved()
    {
        return !$this->isApproved() && !$this->isNew();
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (!$this->getAdditionDate()) {
            $this->setAdditionDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $data = [
            'rating'       => $this->getProduct()->getAverageRating(),
            'maxRating'    => static::MAX_RATING,
            'reviewerName' => $this->getReviewerName(),
            'review'       => $this->getReview(),
        ];

        return \XLite::t('reviewMetaDescription', $data)->translate();
    }

    /**
     * @return string
     */
    public function getURLForProductAdminPage()
    {
        return $this->getProduct()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite\Core\Converter::buildFullURL('product', '', [
                    'product_id'    => $this->getProduct()->getProductId(),
                    'page'          => 'product_reviews'
                ], \XLite::getAdminScript())
            )
            : '';
    }

    /**
     * Send email notification to owner
     *
     * @return string
     */
    public function sendNotificationToOwner()
    {
        return \XLite\Core\Mailer::sendNewReview($this);
    }

    /**
     * Returns code for useForMeta selector
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->getId();
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return $this
     */
    public function setRating($rating)
    {
        $this->rating = max(min($rating, static::MAX_RATING), 1);

        return $this;
    }

    /**
     * Map data to entity columns
     *
     * @param array $data Array of data
     *
     * @return $this
     */
    public function map(array $data)
    {
        $reviewData = [];

        foreach ($data as $key => $value) {
            if ($this->isPropertyExists($key)) {
                $reviewData[$key] = $data[$key];
            }
        }

        return parent::map($reviewData);
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
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set review
     *
     * @param string $review
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;
        return $this;
    }

    /**
     * Return Response
     *
     * @return string
     */
    public function getResponse()
    {
        return (string)$this->response;
    }

    /**
     * Set Response
     *
     * @param string $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set additionDate
     *
     * @param integer $additionDate
     * @return Review
     */
    public function setAdditionDate($additionDate)
    {
        $this->additionDate = $additionDate;
        return $this;
    }

    /**
     * Get additionDate
     *
     * @return integer 
     */
    public function getAdditionDate()
    {
        return $this->additionDate;
    }

    /**
     * Return ResponseDate
     *
     * @return int
     */
    public function getResponseDate()
    {
        return $this->responseDate;
    }

    /**
     * Set ResponseDate
     *
     * @param int $responseDate
     *
     * @return $this
     */
    public function setResponseDate($responseDate)
    {
        $this->responseDate = $responseDate;
        return $this;
    }

    /**
     * Set reviewerName
     *
     * @param string $reviewerName
     * @return Review
     */
    public function setReviewerName($reviewerName)
    {
        $this->reviewerName = $reviewerName;
        return $this;
    }

    /**
     * Get reviewerName
     *
     * @return string 
     */
    public function getReviewerName()
    {
        return $this->reviewerName;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->getProfile()
            ? $this->getProfile()->getLogin()
            : null;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return Review
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set useForMeta
     *
     * @param boolean $useForMeta
     * @return Review
     */
    public function setUseForMeta($useForMeta)
    {
        $this->useForMeta = $useForMeta;
        return $this;
    }

    /**
     * Get useForMeta
     *
     * @return boolean 
     */
    public function getUseForMeta()
    {
        return $this->useForMeta;
    }

    /**
     * Set isNew
     *
     * @param boolean $isNew
     * @return Review
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;
        return $this;
    }

    /**
     * Get isNew
     *
     * @return boolean 
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * Set xcPendingExport
     *
     * @param boolean $xcPendingExport
     * @return Review
     */
    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
        return $this;
    }

    /**
     * Get xcPendingExport
     *
     * @return boolean 
     */
    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }

    /**
     * Set profile
     *
     * @param \XLite\Model\Profile $profile
     * @return Review
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Return Respondent
     *
     * @return \XLite\Model\Profile
     */
    public function getRespondent()
    {
        return $this->respondent;
    }

    /**
     * Set Respondent
     *
     * @param \XLite\Model\Profile $respondent
     *
     * @return $this
     */
    public function setRespondent($respondent)
    {
        $this->respondent = $respondent;
        return $this;
    }

    /**
     * Return respondent name
     *
     * @return string
     */
    public function getRespondentName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Review
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Get reviewKey
     *
     * @return \XLite\Module\XC\Reviews\Model\OrderReviewKey
     */
    public function getReviewKey()
    {
        return $this->reviewKey;
    }

    /**
     * Set reviewKey
     *
     * @param \XLite\Module\XC\Reviews\Model\OrderReviewKey $value
     * @return $this
     */
    public function setReviewKey($value)
    {
        $this->reviewKey = $value;
        return $this;
    }
}
