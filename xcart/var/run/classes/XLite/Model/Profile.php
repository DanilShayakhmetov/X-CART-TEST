<?php
namespace XLite\Model;
/**
 * The "profile" model class
 *
 * @Entity
 * @Table  (name="profiles",
 *      indexes={
 *          @Index (name="cms_profile", columns={"cms_name","cms_profile_id"}),
 *          @Index (name="login", columns={"login"}),
 *          @Index (name="order_id", columns={"order_id"}),
 *          @Index (name="password", columns={"password"}),
 *          @Index (name="access_level", columns={"access_level"}),
 *          @Index (name="first_login", columns={"first_login"}),
 *          @Index (name="last_login", columns={"last_login"}),
 *          @Index (name="status", columns={"status"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Profile extends \XLite\Module\Amazon\PayWithAmazon\Model\Profile {}