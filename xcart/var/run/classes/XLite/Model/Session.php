<?php
namespace XLite\Model;
/**
 * Session
 *
 * @Entity
 * @Table  (name="sessions",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="sid", columns={"sid"})
 *      },
 *      indexes={
 *          @Index (name="expiry", columns={"expiry"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Session extends \XLite\Module\XC\RESTAPI\Model\Session {}