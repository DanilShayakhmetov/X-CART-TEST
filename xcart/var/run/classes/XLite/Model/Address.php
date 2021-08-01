<?php
namespace XLite\Model;
/**
 * Address model
 *
 * @Entity
 * @Table  (name="profile_addresses",
 *      indexes={
 *          @Index (name="is_billing", columns={"is_billing"}),
 *          @Index (name="is_shipping", columns={"is_shipping"})
 *      }
 * )
 * @HasLifecycleCallbacks
 *
 * @method string getFirstname
 * @method string getLastname
 * @method string getStreet
 * @method string getZipcode
 * @method string getCity
 */
class Address extends \XLite\Module\XC\Geolocation\Model\Address {}