<?php
/**
 * @package    TaskBundle\Event\Consumer\Issue
 * @version    $Id: $
 */
namespace TaskBundle\Event\Consumer\Issue;

use TaskBundle\Document\Event;
use TaskBundle\Event\Consumer\Exception\InvalidEventDataException;
use TaskBundle\Event\Notification\Strategy\DestinationStrategyInterface;
use TaskBundle\Repository\IssueMemberRepository;

class ReturnInWorkStrategy implements DestinationStrategyInterface
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var IssueMemberRepository
     */
    private $issueMemberRepository;

    public function __construct(Event $event, IssueMemberRepository $issueMemberRepository)
    {
        $this->event = $event;
        $this->issueMemberRepository = $issueMemberRepository;
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
     * @return array
     * @throws InvalidEventDataException
     */
    public function getDestinationUsers()
    {
        $event = $this->getEvent();
        $data = $event->getData();
        if (empty($data['issue']['id'])) {
            throw new InvalidEventDataException("Can not process event without issue id");
        }

        $eventInitiatorId = (int) $event->getCreatedBy()['id'];
        $users = $this->getIssueMemberRepository()->getUserIdsByIssueId($data['issue']['id']);

        $users = array_filter($users, function($item) use ($eventInitiatorId){
            return $item != $eventInitiatorId;
        });

        return $users;
    }

    /**
     * @return IssueMemberRepository
     */
    public function getIssueMemberRepository()
    {
        return $this->issueMemberRepository;
    }
}
