<?php
/**
 * @package    TaskBundle\Event\Producer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Producer;

interface NotificationInterface
{
    /**
     * Sends message
     *
     * @param string $msgBody
     * @param array $properties
     */
    public function send($msgBody, $properties = array());
}
