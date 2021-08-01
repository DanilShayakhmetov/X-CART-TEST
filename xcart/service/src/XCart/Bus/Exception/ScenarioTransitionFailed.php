<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

use GraphQL\Error\ClientAware;
use Throwable;

class ScenarioTransitionFailed extends \Exception implements ClientAware
{
    /**
     * @var string
     */
    private $idTouched;
    /**
     * @var string
     */
    private $idFailed;
    /**
     * @var string
     */
    private $category;

    /**
     * @param string         $idTouched
     * @param string         $idFailed
     * @param string         $category
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($idTouched, $idFailed, $category, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->idTouched = $idTouched;
        $this->idFailed  = $idFailed;
        $this->category  = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getIdFailed()
    {
        return $this->idFailed;
    }

    /**
     * @param string $idFailed
     */
    public function setIdFailed($idFailed)
    {
        $this->idFailed = $idFailed;
    }

    /**
     * @return string
     */
    public function getIdTouched()
    {
        return $this->idTouched;
    }

    /**
     * @param string $idTouched
     */
    public function setIdTouched($idTouched)
    {
        $this->idTouched = $idTouched;
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @api
     * @return bool
     */
    public function isClientSafe()
    {
        return true;
    }
}
