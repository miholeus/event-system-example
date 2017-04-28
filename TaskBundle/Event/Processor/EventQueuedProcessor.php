<?php
/**
 * @package    TaskBundle\Event\Processor
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Processor;

use TaskBundle\Event\Consumer\EventStrategy;
use TaskBundle\Event\Notification\Strategy\DestinationStrategyInterface;
use TaskBundle\Event\Producer\NotificationInterface;
use TaskBundle\Repository\Document\EventRepository;

class EventQueuedProcessor implements ProcessorInterface
{
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var NotificationInterface
     */
    private $producer;
    /**
     * @var EventStrategy
     */
    private $eventStrategy;

    public function __construct(EventRepository $eventRepository, NotificationInterface $producer, EventStrategy $eventStrategy)
    {
        $this->eventRepository = $eventRepository;
        $this->producer = $producer;
        $this->eventStrategy = $eventStrategy;
    }

    /**
     * Process event
     *
     * @param mixed $event
     * @return bool
     */
    public function process($event)
    {
        if (!$event instanceof \TaskBundle\Document\Event) {
            return false;
        }
        $addressedTo = $this->getDestinationStrategy($event)->getDestinationUsers();
        if (empty($addressedTo)) {
            return false;
        }
        $event->setAddressedTo($addressedTo);
        $event->setQueued(false);
        $event->setUpdatedOn(new \DateTime());

        $this->getEventRepository()->save($event);
        $this->getProducer()->send(serialize($event));
    }

    /**
     * @param \TaskBundle\Document\Event $event
     * @return DestinationStrategyInterface
     */
    public function getDestinationStrategy(\TaskBundle\Document\Event $event)
    {
        $strategy = $this->getEventStrategy();
        $strategy->setEvent($event);
        return $strategy->getDestinationStrategy();
    }

    /**
     * @return EventRepository
     */
    public function getEventRepository()
    {
        return $this->eventRepository;
    }

    /**
     * @return NotificationInterface
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * @return EventStrategy
     */
    public function getEventStrategy()
    {
        return $this->eventStrategy;
    }
}
