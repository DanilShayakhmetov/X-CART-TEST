<?php
namespace XLite\Module\CDev\SimpleCMS\Model\Repo;
/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\SimpleCMS\Model\Page", summary="Add static page")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\SimpleCMS\Model\Page", summary="Retrieve static page by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\SimpleCMS\Model\Page", summary="Retrieve static pages by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\SimpleCMS\Model\Page", summary="Update static page by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\SimpleCMS\Model\Page", summary="Delete static page by id")
 *
 * @SWG\Tag(
 *   name="CDev\SimpleCMS\Page",
 *   x={"display-name": "Page", "group": "CDev\SimpleCMS"},
 *   description="Page repo contains the static pages of the site.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about adding pages to your store",
 *     url="https://kb.x-cart.com/en/look_and_feel/adding_pages_to_your_store.html"
 *   )
 * )
 */
class Page extends \XLite\Module\CDev\GoSocial\Module\CDev\SimpleCMS\Model\Repo\Page {}