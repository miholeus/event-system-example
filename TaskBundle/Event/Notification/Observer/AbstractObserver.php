<?php
/**
 * @package    TaskBundle\Event\Notification\Observer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification\Observer;

use TaskBundle\Event\Producer\NotificationInterface;

abstract class AbstractObserver implements \SplObserver
{
    /**
     * @var NotificationInterface
     */
    protected $producer;

    public function __construct(NotificationInterface $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @return NotificationInterface
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * Unique identifier of observer
     *
     * @return mixed
     */
    abstract public function getId();
}
