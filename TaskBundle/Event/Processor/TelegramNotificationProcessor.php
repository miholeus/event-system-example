<?php
/**
 * @package    TaskBundle\Event\Processor
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */


namespace TaskBundle\Event\Processor;


use Monolog\Logger;
use TaskBundle\Service\Notification\TelegramNotificationService;

class TelegramNotificationProcessor implements ProcessorInterface
{
    /**
     * @var TelegramNotificationService
     */
    private $notificationService;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(TelegramNotificationService $notificationService, Logger $logger)
    {
        $this->notificationService = $notificationService;
        $this->logger = $logger;
    }

    /**
     * @param mixed $event
     * @return bool
     * @throws \Exception
     */
    public function process($event)
    {
        if (empty($event['chat_id'])) {// if chat identifier is not set, message will not be delivered
            return false;
        }
        $extra = [];
        if (!empty($event['action'])) {
            $extra = $event['action'];
        }
        $fields = [
            'parse_mode',
            'disable_web_page_preview',
            'disable_notification',
            'reply_to_message_id',
            'reply_markup'
        ];
        foreach ($fields as $field) {
            if (isset($event[$field])) {
                $extra[$field] = $event[$field];
            }
        }

        try {
            $this->getNotificationService()->send($event['message'], $event['chat_id'], $extra);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }

    /**
     * @return TelegramNotificationService
     */
    public function getNotificationService()
    {
        return $this->notificationService;
    }

}
