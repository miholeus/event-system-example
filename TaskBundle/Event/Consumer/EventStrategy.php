<?php
/**
 * @package    TaskBundle\Event\Consumer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Consumer;


use Symfony\Component\DependencyInjection\ContainerInterface;
use TaskBundle\Event\Consumer\Exception\InvalidEventDataException;
use TaskBundle\Event\Consumer\Exception\InvalidEventTypeException;
use TaskBundle\Event\Consumer\Issue\IssueMembersStrategy;
use TaskBundle\Event\Consumer\Issue\ReturnInWorkStrategy;
use TaskBundle\Event\Consumer\Project\AddAssigneeStrategy;
use TaskBundle\Event\Issue\DueDateChangeEvent;
use TaskBundle\Event\Issue\ReturnInWorkEvent;
use TaskBundle\Event\Notification\Strategy\DestinationStrategyInterface;
use TaskBundle\Event\Project\AddAssigneeEvent;

class EventStrategy
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var \TaskBundle\Document\Event
     */
    protected $event;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets event strategy
     *
     * @throws InvalidEventDataException
     * @throws InvalidEventTypeException
     * @return DestinationStrategyInterface
     */
    public function getDestinationStrategy()
    {
        $event = $this->getEvent();

        switch ($event->getType()) {
            case (new DueDateChangeEvent())->getName():
                return new IssueMembersStrategy($event, $this->getContainer()->get('repository.issue_repository'));
                break;
            case (new ReturnInWorkEvent())->getName():
                return new ReturnInWorkStrategy($event, $this->getContainer()->get('repository.issue_member_repository'));
                break;
            case (new AddAssigneeEvent())->getName():
                return new AddAssigneeStrategy($event);
                break;
            default:
                throw new InvalidEventTypeException(sprintf("Unknown event type provided: %s", $event->getType()));
        }
    }

    /**
     * @return \TaskBundle\Document\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param \TaskBundle\Document\Event $event
     */
    public function setEvent(\TaskBundle\Document\Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
