<?php
/**
 * @package    TaskBundle\Event\Consumer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use TaskBundle\Document\Event;
use TaskBundle\Event\Processor\ProcessorInterface;

class NotificationConsumer
{
    /**
     * @var ProcessorInterface
     */
    private $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Creates new notification from event source
     * Publishes to notification sender queue
     * Gets notifications from "notification.new" queue
     *
     * @param AMQPMessage $message
     * @return bool
     */
    public function execute(AMQPMessage $message)
    {
        $body = $message->getBody();

        /** @var Event $event */
        $event = @unserialize($body);
        if (false === $event) {// error in deserialization
            return true;// discard message
        }
        if ($event->getDispatched()) {
            return true;// remove message from queue
        }

        $this->getProcessor()->process($event);
    }

    /**
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }
}
