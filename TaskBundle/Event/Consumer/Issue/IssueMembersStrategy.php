<?php
/**
 * @package    TaskBundle\Event\Consumer\Issue
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */
namespace TaskBundle\Event\Consumer\Issue;

use TaskBundle\Document\Event;
use TaskBundle\Event\Consumer\Exception\InvalidEventDataException;
use TaskBundle\Event\Notification\Strategy\DestinationStrategyInterface;
use TaskBundle\Repository\IssueRepository;

class IssueMembersStrategy implements DestinationStrategyInterface
{
    /**
     * @var Event
     */
    private $event;
    /**
     * @var IssueRepository
     */
    private $issueRepository;

    public function __construct(Event $event, IssueRepository $issueRepository)
    {
        $this->event = $event;
        $this->issueRepository = $issueRepository;
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
            throw new InvalidEventDataException("Can not process event with unknown issue id");
        }
        $issueId = $data['issue']['id'];
        $repo = $this->getIssueRepository();
        /** @var \TaskBundle\Entity\Issue $issue */
        $issue = $repo->find($issueId);
        // @todo get filtered user ids from sql
        $members = $issue->getMembers();
        $users = [];
        foreach ($members as $member) {
            if (!in_array($member->getUser()->getId(), $users)) {
                $users[] = $member->getUser()->getId();
            }
        }
        return $users;
    }

    /**
     * @return IssueRepository
     */
    public function getIssueRepository()
    {
        return $this->issueRepository;
    }
}
