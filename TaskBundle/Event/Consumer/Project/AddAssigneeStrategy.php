<?php
/**
 * @package    TaskBundle\Event\Consumer\Project
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Consumer\Project;

use TaskBundle\Document\Event;
use TaskBundle\Event\Notification\Strategy\DestinationStrategyInterface;

class AddAssigneeStrategy implements DestinationStrategyInterface
{
    /**
     * @var Event
     */
    private $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Users already defined in this event
     *
     * @return array
     */
    public function getDestinationUsers()
    {
        return $this->getEvent()->getAddressedTo();
    }
}
