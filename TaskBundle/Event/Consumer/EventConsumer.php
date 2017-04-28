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

/**
 * Creates events and notifies notification consumer
 */
class EventConsumer
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
     * Creates new event with destination users for future notifications
     * Publishes to "notification.new" queue
     * @see NotificationConsumer
     *
     * @param AMQPMessage $message
     * @return bool
     */
    public function execute(AMQPMessage $message)
    {
        $body = $message->getBody();

//        $message->delivery_info['channel']->basic_ack()
        /** @var Event $event */
        $event = @unserialize($body);
        if (false === $event) {// error in deserialization
            return true;// discard message
        }
        if ($event->getDispatched() || !$event->getQueued()) {
            return true;// remove message from queue
        }

        $this->getProcessor()->process($event);

        return $message->delivery_info['consumer_tag'];
    }

    /**
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }
}
