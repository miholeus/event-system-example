<?php
/**
 * @package    TaskBundle\Event
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event;
/**
 * Interface EventInChainInterface
 * Mainly used for prefixed events.
 * For example, if any event is a part of other events, it should be prefixed with main event
 * for better understanding of domain processes
 */
interface EventInChainInterface extends EventInterface
{
    /**
     * Get prefix for event
     *
     * @return string
     */
    public function getPrefix();
}
