<?php
/**
 * @package    TaskBundle\Event
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event;
/**
 * Interface NotificationInterface
 * Main interface for notification system
 */
interface NotificationInterface
{
    /**
     * Notify about triggered event
     *
     * @param $event
     * @return mixed
     */
    public function notify(Event $event);
}