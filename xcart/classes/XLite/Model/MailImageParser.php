<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use XLite\Core\ImageOperator\ADTO;

/**
 * Mail images parser
 * TODO: full refactoring is required
 */
class MailImageParser extends \XLite\Core\FlexyCompiler
{
    /**
     * webdir
     *
     * @var string
     */
    public $webdir;

    /**
     * images
     *
     * @var array
     */
    public $images;

    /**
     * counter
     *
     * @var integer
     */
    public $counter;


    /**
     * Constructor
     * FIXME - we must found anoither way... now it is antipattern Public Morozov
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * flexy
     *
     * @return void
     */
    public function flexy()
    {
    }

    /**
     * postprocess
     *
     * @return void
     */
    public function postprocess()
    {
        $name = '';
        $this->images = [];
        $this->counter = 1;

        // find images, e.g. background=..., src=..., style="...url('...')"
        foreach ($this->tokens as $key => $token) {
            if ($token['type'] === 'attribute') {
                $name = strtolower($token['name']);
            } elseif ($token['type'] === 'attribute-value') {
                $val = $this->getTokenText($key);

                if ($name === 'style') {
                    // find url(<...>) or url ('<...>')
                    preg_match_all('/url\([\'" ]?([^ "\'()]+)[\'" ]?\)/', $val, $matches);
                    list(, $urls) = $matches;

                    foreach ($urls as $url) {
                        $start = $token['start'] + strpos($val, $url);
                        $end = $start + strlen($url);
                        $this->substImage($start, $end);
                    }
                } elseif ($name === 'background' || $name === 'src') {
                    $this->substImage($token['start'], $token['end']);
                }

                $name = '';
            } else {
                $name = '';
            }
        }

        $this->result = $this->substitute();
    }

    /**
     * substImage
     *
     * @param mixed $start ____param_comment____
     * @param mixed $end   ____param_comment____
     *
     * @return void
     */
    public function substImage($start, $end)
    {
        $img = substr($this->source, $start, $end - $start);

        if (strcasecmp(substr($img, 0, 2), '//') === 0) {
            $img = 'http:' . $img;
        }

        if (
            strcasecmp(substr($img, 0, 5), 'http:') !== 0
            && strcasecmp(substr($img, 0, 6), 'https:') !== 0
        ) {
            $img = $this->webdir . $img; // relative URL
        }

        $img = str_replace('&amp;', '&', $img);
        $img = str_replace(' ', '%20', $img);

        $this->subst($start, $end, $this->getImgSubstitution($img));
    }

    /**
     * getImgSubstitution
     *
     * @param mixed $img ____param_comment____
     *
     * @return string
     */
    public function getImgSubstitution($img)
    {
        if (!isset($this->images[$img])) {

            $image = ADTO::getDTO($img);
            if ($image->getBody()) {
                $path = tempnam(LC_DIR_COMPILE, 'mailimage');
                file_put_contents($path, $image->getBody());

                $this->images[$img] = array(
                    'name' => $image->getName(),
                    'path' => $path,
                    'mime' => $image->getType()
                );

                $this->counter++;

            } else {
                return $img;
            }
        }

        return 'cid:' . $this->images[$img]['name'] . '@mail.lc';
    }

    /**
     * Removes any images that were parsed
     *
     * @return void
     */
    public function unlinkImages()
    {
        foreach ($this->images as $image) {

            \Includes\Utils\FileManager::deleteFile($image['path']);
        }
    }
}
