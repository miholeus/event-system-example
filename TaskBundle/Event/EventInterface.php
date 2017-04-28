<?php
/**
 * @package    TaskBundle\Event
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event;
/**
 * Interface for events
 * Each event should have name
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getName();
}