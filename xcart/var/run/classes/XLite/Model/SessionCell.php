<?php
namespace XLite\Model;
/**
 * Session
 *
 * @Entity
 * @Table  (name="session_cells",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="iname", columns={"id", "name"})
 *      },
 *      indexes={
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class SessionCell extends \XLite\Module\XC\RESTAPI\Model\SessionCell {}