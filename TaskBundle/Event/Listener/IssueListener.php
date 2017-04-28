<?php
/**
 * @package    TaskBundle\Event\Listener
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Listener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use TaskBundle\Document\Event;
use TaskBundle\Entity\Issue;
use TaskBundle\Entity\IssueWorkflowStatus;
use TaskBundle\Event\Issue\DueDateChangeEvent;
use TaskBundle\Event\Issue\IssueRelatesUpdateEvent;
use TaskBundle\Event\Issue\ReturnInWorkEvent;
use TaskBundle\Repository\IssueCommentRepository;
use TaskBundle\Service\IssueRelationService;

/**
 * Issue listeners listens to specified events on issue
 * These listener is attached into main issue workflow event system
 */
class IssueListener
{
    /**
     * Sends messages to broker
     *
     * @var ProducerInterface
     */
    private $producer;
    /**
     * @var IssueCommentRepository
     */
    private $commentRepository;

    private $relationService;

    public function __construct(ProducerInterface $producer, IssueCommentRepository $commentRepository, IssueRelationService $relationService)
    {
        $this->producer = $producer;
        $this->commentRepository = $commentRepository;
        $this->relationService = $relationService;
    }

    /**
     * Event is dispatched when due date is changed
     * Publishes event to "event.new" queue
     * @see EventConsumer
     * @param DueDateChangeEvent $event
     */
    public function onUpdateDueDateEvent(DueDateChangeEvent $event)
    {
        $eventItem = new Event();
        /** @var Issue $issue */
        $issue = $event->getSubject();

        $eventItem->setName($event->getDescription());
        $eventItem->setType($event->getName());
        $eventItem->setCreatedBy($event->getArgument('user'));
        $dueDate = $event->getArgument('dueDate');

        $eventItem->setData([
            'issue' => [
                'id' => $issue->getId(),
                'name' => $issue->getName()
            ],
            'changeset' => [
                'old' => $this->formatDate($dueDate[0]),
                'new' => $this->formatDate($dueDate[1])
            ]
        ]);

        // publish event to broker
        $routeKey = sprintf('issue.%d.events', $issue->getId());
        $this->getProducer()->publish(serialize($eventItem), $routeKey);
    }

    /**
     * Event is dispatched when issue is returned in work from statuses "done" or "finishing"
     * Publishes event to "event.new" queue
     * @see EventConsumer
     * @param ReturnInWorkEvent $event
     */
    public function onReturnInWorkEvent(ReturnInWorkEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->getSubject();
        $project = $issue->getProject();

        $eventItem = new Event();
        $eventItem->setName($event->getDescription());
        $eventItem->setType($event->getName());
        $eventItem->setCreatedBy($event->getArgument('user'));

        /** @var IssueWorkflowStatus $workflowStatus */
        $workflowStatus = $event->getArgument('oldWorkflowStatus');

        $eventItem->setData([
            'issue' => [
                'id' => $issue->getId(),
                'name' => $issue->getName()
            ],
            'project' => [
                'id' => $project->getId(),
                'name' => $project->getName()
            ],
            'old_workflow_status' => [
                'id' => $workflowStatus->getId(),
                'code' => $workflowStatus->getCode(),
                'name' => $workflowStatus->getName()
            ],
            'reason' => $event->hasArgument('reason') ? $event->getArgument('reason') : null
        ]);

        if ($event->hasArgument('reason')) {
            // create issue comment
            $repo = $this->getCommentRepository();
            $repo->addGeneratedComment($issue, $event->getArgument('reason'), $event->getArgument('user'));
        }

        // publish event to broker
        $routeKey = sprintf('issue.%d.events', $issue->getId());
        $this->getProducer()->publish(serialize($eventItem), $routeKey);
    }

    /**
     * @param IssueRelatesUpdateEvent $event
     */
    public function onUpdateDateEvent(IssueRelatesUpdateEvent $event)
    {
        $this->getRelationService()->saveRelations($event->getSubject());
    }

    /**
     * Formats date
     *
     * @param \DateTime|null $dt
     * @return string
     */
    private function formatDate(\DateTime $dt = null)
    {
        $result = '';
        if ($dt) {
            $result = $dt->format('d.m.Y');
        }
        return $result;
    }

    /**
     * @return ProducerInterface
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * @return IssueCommentRepository
     */
    public function getCommentRepository()
    {
        return $this->commentRepository;
    }

    /**
     * @return IssueRelationService
     */
    public function getRelationService()
    {
        return $this->relationService;
    }
}
