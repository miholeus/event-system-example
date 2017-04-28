<?php
/**
 * @package    TaskBundle\Event\Producer
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Producer;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

/**
 * Sender encapsulates data about routing key and producer
 */
class NotificationSender implements NotificationInterface
{
    /**
     * @var
     */
    private $routingKey;
    /**
     * @var ProducerInterface
     */
    private $producer;

    public function __construct($routingKey, ProducerInterface $producer)
    {
        $this->routingKey = $routingKey;
        $this->producer = $producer;
    }

    /**
     * Sends notification
     *
     * @param string $msgBody
     * @param array $properties
     */
    public function send($msgBody, $properties = array())
    {
        $this->getProducer()->publish($msgBody, $this->getRoutingKey(), $properties);
    }
    /**
     * @return ProducerInterface
     */
    public function getProducer()
    {
        return $this->producer;
    }

    /**
     * @return mixed
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }
}
