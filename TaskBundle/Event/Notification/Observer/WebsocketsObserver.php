<?php
/**
 * @package    TaskBundle\Event\Notification\Observer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification\Observer;

use TaskBundle\Event\Consumer\PushNotification\PushNotificationManager;

class WebsocketsObserver extends AbstractObserver
{
    public function update(\SplSubject $subject)
    {
        /** @var PushNotificationManager $subject */
        $notification = $subject->getNotification();

        $payload = $notification->getPayload();
        $payload['channel'] = $notification->getChannel();

        $this->getProducer()->send(json_encode($payload));
    }

    public function getId()
    {
        return 'websockets';
    }
}
