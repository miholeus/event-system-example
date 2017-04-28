<?php
/**
 * @package    TaskBundle\Event\Processor
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Processor;

use TaskBundle\Repository\Document\EventRepository;
use TaskBundle\Service\Event\NotificationService;

class EventDispatchedProcessor implements ProcessorInterface
{
    /**
     * @var NotificationService
     */
    private $service;
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(NotificationService $service, EventRepository $eventRepository)
    {
        $this->service = $service;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param mixed $event
     * @return int
     */
    public function process($event)
    {
        if (!$event instanceof \TaskBundle\Document\Event) {
            return false;
        }
        $notifications = 0;

        try {
            $repo = $this->getService()->getNotificationRepository();

            foreach($this->getService()->createNotifications($event) as $notification) {
                // save notification to repository
                $repo->save($notification);

                $this->getService()->notify($notification);

                ++$notifications;
            }
        } catch (\Exception $e) {
            $event->setComment($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }

        $event->setDispatched(true);
        $event->setUpdatedOn(new \DateTime());
        $this->getEventRepository()->save($event);
        return $notifications;
    }

    /**
     * @return NotificationService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return EventRepository
     */
    public function getEventRepository()
    {
        return $this->eventRepository;
    }
}
