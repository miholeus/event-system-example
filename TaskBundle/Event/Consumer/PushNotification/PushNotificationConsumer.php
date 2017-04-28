<?php
/**
 * @package    TaskBundle\Event\Consumer\PushNotification
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */
namespace TaskBundle\Event\Consumer\PushNotification;

use PhpAmqpLib\Message\AMQPMessage;
use TaskBundle\Event\Processor\ProcessorInterface;

/**
 * Basic push notifications sender
 */
class PushNotificationConsumer
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
     *
     *
     * @param AMQPMessage $message
     * @return bool
     */
    public function execute(AMQPMessage $message)
    {
        $body = $message->getBody();

        $data = json_decode($body, true);

        $this->getProcessor()->process($data);

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
