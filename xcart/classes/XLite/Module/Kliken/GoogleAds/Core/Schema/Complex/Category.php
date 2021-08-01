<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema\Complex;

class Category implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
{
    private $rootCategoryId;

    public function __construct()
    {
        $this->rootCategoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
    }

    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity $model            Entity
     * @param boolean              $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $language = \XLite\Core\Config::getInstance()->General->default_language;
        $translation = $model->getSoftTranslation($language);

        $cleanUrls = [];
        foreach ($model->getCleanURLs() as $cleanURL) {
            $cleanUrls[] = $cleanURL->getCleanURL();
        }

        return [
            'isRootCategory' => $model->isRootCategory(),
            'categoryId'     => $model->getCategoryId(),
            'parentId'       => $model->getParentId() == $this->rootCategoryId ? null : $model->getParentId(),
            'enabled'        => $model->getEnabled(),
            'image'          => $model->getImage() ? $model->getImage()->getFrontURL() : null,
            'name'           => $translation->getName(),
            'description'    => $translation->getDescription(),
            'cleanUrls'      => $cleanUrls,
        ];
    }

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    public function prepareInput(array $data)
    {
        return [true, $data];
    }

    /**
     * Preload data
     *
     * @param \XLite\Model\AEntity $entity Product
     * @param array                $data   Data
     *
     * @return void
     */
    public function preloadData(\XLite\Model\AEntity $entity, array $data)
    {
    }
}
