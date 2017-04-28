<?php
/**
 * @package    TaskBundle\Event\Processor
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Processor;


use TaskBundle\Service\Notification\APNsNotificationService;

class iOSNotificationProcessor implements ProcessorInterface
{
    /**
     * @var APNsNotificationService
     */
    private $notificationService;

    public function __construct(APNsNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @param mixed $event
     * @return bool
     */
    public function process($event)
    {
        if (empty($event['device_token'])) {// if identifier is not set, then message can't be delivered
            return false;
        }
        $this->getNotificationService()->send($event['message'], $event['device_token']);
    }

    /**
     * @return APNsNotificationService
     */
    public function getNotificationService()
    {
        return $this->notificationService;
    }
}
