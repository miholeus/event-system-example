<?php
/**
 * @package    TaskBundle\Event\Listener
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Listener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use TaskBundle\Document\Event;
use TaskBundle\Entity\Project;
use TaskBundle\Entity\User;
use TaskBundle\Event\Consumer\PushNotification\PushNotificationManager;
use TaskBundle\Event\Project\AddAssigneeEvent;
use TaskBundle\Event\Project\ConfirmAssigneeEvent;
use TaskBundle\Event\Exception;

/**
 * Project listeners listens to specified events on project
 * These listener is attached into main project workflow event system
 */
class ProjectListener
{
    /**
     * Sends messages to broker
     *
     * @var ProducerInterface
     */
    private $producer;
    /**
     * @var PushNotificationManager
     */
    private $notificationManager;

    public function __construct(ProducerInterface $producer, PushNotificationManager $notificationManager)
    {
        $this->producer = $producer;
        $this->notificationManager = $notificationManager;
    }

    /**
     * Event is dispatched when new assignee is added to project
     * Publishes event to "event.new" queue
     * @see EventConsumer
     *
     * @param AddAssigneeEvent $event
     */
    public function onAddAssigneeEvent(AddAssigneeEvent $event)
    {
        $eventItem = new Event();
        /** @var Project $project */
        $project = $event->getSubject();
        $eventItem->setAddressedTo([$project->getAssignedTo()->getId()]);
        $eventItem->setName($event->getDescription());
        $eventItem->setType($event->getName());
        $eventItem->setCreatedBy($event->getArgument('user'));
        /** @var \TaskBundle\Entity\User $assignee */
        $assignee = $event->hasArgument('assigned_to_before') ? $event->getArgument('assigned_to_before') : null;

        if (null === $assignee) {
            $lastAssignee = null;
        } else {
            $lastAssignee = [
                'id' => $assignee->getId(),
                'first_name' => $assignee->getFirstname(),
                'last_name' => $assignee->getLastname(),
                'middle_name' => $assignee->getMiddlename()
            ];
        }

        $eventItem->setData([
            'project_id' => $project->getId(),
            'project_name' => $project->getName(),
            'project_photo' => $project->getPhoto(),
            'assignee' => [
                'id' => $project->getAssignedTo()->getId(),
                'first_name' => $project->getAssignedTo()->getFirstname(),
                'last_name' => $project->getAssignedTo()->getLastname(),
                'middle_name' => $project->getAssignedTo()->getMiddlename()
            ],
            'last_assignee' => $lastAssignee
        ]);

        // publish event to broker
        $routeKey = sprintf('project.%d.events', $project->getId());
        $this->getProducer()->publish(serialize($eventItem), $routeKey);
    }

    public function onConfirmAssigneeEvent(ConfirmAssigneeEvent $event)
    {
        /** @var Project $project */
        $project = $event->getSubject();

        $action = $event->getArgument('action');
        /** @var User $user */
        $user = $event->getArgument('user');

        if ($project->getAssignedTo()->getId() != $user->getId()) {
            throw new Exception(sprintf("User (%s) is not assigned as assignee to accept/cancel project (%s)",
                $user->getId(), $project->getId()));
        }

        // @todo save info in storage
        if ($action == ConfirmAssigneeEvent::ACTION_ACCEPT) {
            $message = 'Руководитель назначен успешно';
        } elseif($action == ConfirmAssigneeEvent::ACTION_CANCEL) {
            $message = 'Назначение руководителя отменено';
        } else {
            throw new Exception(sprintf("Unknown confirm assignee action (%s)", $action));
        }

        // do not use action and buttons fields, it may cause infinite loop for RabbitMQ!!!
        $payload = [
            'message' => $message,
            'reply_to_message_id' => $event->hasArgument('reply_to') ? $event->getArgument('reply_to') : null
        ];

        $notification = new \TaskBundle\Document\Notification();
        $notification->setAddressedTo($user->getId());
        $notification->setPayload($payload);

        // notify user
        $this->getNotificationManager()->setNotification($notification);
        $this->getNotificationManager()->notifyById('telegram');
    }

    /**
     * @return ProducerInterface
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * @return PushNotificationManager
     */
    public function getNotificationManager()
    {
        return $this->notificationManager;
    }
}
