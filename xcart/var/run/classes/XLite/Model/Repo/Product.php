<?php
namespace XLite\Model\Repo;
/**
 * The "product" model repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Product", summary="Add new product")
 * @Api\Operation\Read(modelClass="XLite\Model\Product", summary="Retrieve product by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Product", summary="Retrieve product by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Product", summary="Update product by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Product", summary="Delete product by id")
 *
 * @SWG\Tag(
 *   name="Product",
 *   description="Product is the building block of your store. It contains data about certain good you trade and is identified by SKU. Product is tightly coupled with its Category and Attributes.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about product fields and options",
 *     url="https://kb.x-cart.com/products/adding_products.html"
 *   )
 * )
 */
class Product extends \XLite\Module\CDev\FeaturedProducts\Model\Repo\Product {}