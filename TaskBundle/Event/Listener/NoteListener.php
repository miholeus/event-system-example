<?php
/**
 * @package    TaskBundle\Event\Listener
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */


namespace TaskBundle\Event\Listener;

use TaskBundle\Entity\Note;
use TaskBundle\Service\Note as NoteService;
use TaskBundle\Event\Consumer\PushNotification\PushNotificationManager;
use TaskBundle\Event\Note\AddNoteEvent;

class NoteListener
{
    /**
     * @var PushNotificationManager
     */
    private $notificationManager;
    /**
     * @var NoteService
     */
    private $service;

    public function __construct(NoteService $service, PushNotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
        $this->service = $service;
    }

    /**
     * Add note event
     *
     * @param AddNoteEvent $event
     * @throws \TaskBundle\Event\Consumer\PushNotification\Exception
     */
    public function onAddEvent(AddNoteEvent $event)
    {
        /** @var Note $note */
        $note = $event->getSubject();

        $this->getService()->save($note);

        // do not use action and buttons fields, it may cause infinite loop for RabbitMQ!!!
        $payload = [
            'message' => "Заметка создана"
        ];
        $user = $note->getCreatedBy();

        $notification = new \TaskBundle\Document\Notification();
        $notification->setAddressedTo($user->getId());
        $notification->setPayload($payload);

        // notify user
        $this->getNotificationManager()->setNotification($notification);
        $this->getNotificationManager()->notifyById('telegram');
    }

    /**
     * @return PushNotificationManager
     */
    public function getNotificationManager()
    {
        return $this->notificationManager;
    }

    /**
     * @return NoteService
     */
    public function getService()
    {
        return $this->service;
    }
}
