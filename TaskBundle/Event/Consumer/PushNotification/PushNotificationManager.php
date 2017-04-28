<?php
/**
 * @package    TaskBundle\Event\Consumer\PushNotification
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Consumer\PushNotification;

/**
 * This class implements observer pattern strategy
 */
class PushNotificationManager implements \SplSubject
{
    /**
     * @var \SplObserver[]
     */
    protected $senders;
    /**
     * @var \TaskBundle\Document\Notification
     */
    protected $notification;

    public function attach(\SplObserver $observer)
    {
        $this->senders[spl_object_hash($observer)] = $observer;
    }

    public function detach(\SplObserver $observer)
    {
        $hash = spl_object_hash($observer);
        if (isset($this->senders[$hash])) {
            unset($this->senders[$hash]);
            return true;
        }
        return false;
    }

    /**
     * Notifies all observers
     *
     * @throws Exception
     */
    public function notify()
    {
        if (null === $this->getNotification()) {
            throw new Exception("Push notification manager needs notification to be set");
        }
        foreach ($this->senders as $sender) {
            $sender->update($this);
        }
    }

    /**
     * @return \TaskBundle\Document\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param \TaskBundle\Document\Notification $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Observer with selected id will be notified
     *
     * @param $id
     * @throws Exception
     */
    public function notifyById($id)
    {
        $observer = null;
        foreach($this->senders as $sender) {
            if ($sender->getId() == $id) {
                $observer = $sender;
                break;
            }
        }
        if (null === $observer) {
            throw new Exception(sprintf("Notification sender by id %s not found", $id));
        }
        $observer->update($this);
    }
}
