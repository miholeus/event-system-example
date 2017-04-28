<?php
/**
 * @package    TaskBundle\Event\Notification\Observer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification\Observer;

use SplSubject;
use TaskBundle\Event\Consumer\PushNotification\PushNotificationManager;
use TaskBundle\Event\Producer\NotificationInterface;
use TaskBundle\Repository\TelegramChatRepository;

class TelegramObserver extends AbstractObserver
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;

    public function __construct(NotificationInterface $producer, TelegramChatRepository $repository)
    {
        parent::__construct($producer);
        $this->repository = $repository;
    }

    /**
     * @param SplSubject $subject
     * @return bool
     */
    public function update(SplSubject $subject)
    {
        /** @var PushNotificationManager $subject */
        $notification = $subject->getNotification();
        $payload = $notification->getPayload();
        // detect user chat_id
        $chat = $this->getRepository()->findUserChatId($notification->getAddressedTo());

        if (null === $chat) {// user has no association with telegram
            return false;
        }
        $payload['chat_id'] = $chat->getChatId();

        $this->getProducer()->send(json_encode($payload));
    }

    /**
     * @return TelegramChatRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function getId()
    {
        return 'telegram';
    }
}
