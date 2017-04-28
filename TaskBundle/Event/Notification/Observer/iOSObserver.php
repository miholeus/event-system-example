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
use TaskBundle\Repository\DeviceTokenRepository;

class iOSObserver extends AbstractObserver
{
    /**
     * @var DeviceTokenRepository
     */
    private $deviceTokenRepository;

    public function __construct(NotificationInterface $producer, DeviceTokenRepository $deviceTokenRepository)
    {
        parent::__construct($producer);
        $this->deviceTokenRepository = $deviceTokenRepository;
    }

    public function update(SplSubject $subject)
    {
        /** @var PushNotificationManager $subject */
        $notification = $subject->getNotification();
        $payload = $notification->getPayload();

        $deviceTokens = $this->getUserDeviceTokens($notification->getAddressedTo());

        foreach ($deviceTokens as $deviceToken) {
            $pushData = $payload;

            $pushData['device_token'] = $deviceToken;
            $this->getProducer()->send(json_encode($pushData));
        }
    }

    /**
     * Fetches user's device tokens
     *
     * @param $userId
     * @return array
     */
    protected function getUserDeviceTokens($userId)
    {
        static $tokens;
        if (!empty($tokens[$userId])) {
            return $tokens[$userId];
        }
        $data = [];
        $repo = $this->getDeviceTokenRepository();
        $tokens = $repo->getUserTokens($userId);
        array_walk($tokens, function($value) use (&$data) {
            $data[] = $value['token'];
        });
        $tokens[$userId] = $data;
        return $tokens[$userId];
    }

    /**
     * @return DeviceTokenRepository
     */
    public function getDeviceTokenRepository()
    {
        return $this->deviceTokenRepository;
    }

    public function getId()
    {
        return 'iOS';
    }
}
