<?php
/**
 * @package    TaskBundle\Event\Notification
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification;

use TaskBundle\Document\Notification;

/**
 * Creates iterator for many notifications
 */
class Iterator implements \Iterator
{
    /**
     * Notification channel
     *
     * @var string
     */
    protected $notificationChannel;
    /**
     * Notification type
     *
     * info, action and others
     *
     * @var string
     */
    protected $notificationType;
    /**
     * User to be notified
     *
     * @var array
     */
    protected $addressedTo;
    /**
     * Notification message
     *
     * @var string
     */
    protected $message;
    /**
     * Additional information
     *
     * @var array
     */
    protected $extra;
    /**
     * Internal pointer
     *
     * @var int
     */
    protected $offset;

    /**
     * Creates notification
     *
     * @param $addressedTo
     * @return Notification
     */
    protected function createNotification($addressedTo)
    {
        $notification = new Notification();
        $notification->setAddressedTo($addressedTo);

        if (null === $this->getNotificationChannel()) {
            $notification->setChannel(sprintf('notifications.user.%d', $addressedTo));
        } else {
            $notification->setChannel($this->getNotificationChannel());
        }
        return $notification;
    }

    public function __construct(array $addressedTo)
    {
        $this->addressedTo = $addressedTo;
        $this->offset = 0;
        $this->notificationType = Notification::TYPE_INFO;
    }

    public function current()
    {
        $addressedTo = $this->addressedTo[$this->offset];
        $notification = $this->createNotification($addressedTo);
        return $notification;
    }

    public function next()
    {
        ++$this->offset;
    }

    public function key()
    {
        return $this->offset;
    }

    public function valid()
    {
        return isset($this->addressedTo[$this->offset]);
    }

    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * @param mixed $notificationChannel
     */
    public function setNotificationChannel($notificationChannel)
    {
        $this->notificationChannel = $notificationChannel;
    }

    /**
     * @return mixed
     */
    public function getNotificationChannel()
    {
        return $this->notificationChannel;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * @param string $notificationType
     */
    public function setNotificationType($notificationType)
    {
        $this->notificationType = $notificationType;
    }
}
