<?php

/**
 * @package    TaskBundle\Event\Notification
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification;

use TaskBundle\Document\Event;
use TaskBundle\Document\Notification;
use TaskBundle\Event\EventFactory;
use TaskBundle\Event\Issue\DueDateChangeEvent;
use TaskBundle\Event\Issue\ReturnInWorkEvent;
use TaskBundle\Event\Project\AddAssigneeEvent;
use TaskBundle\Event\Project\ConfirmAssigneeEvent;

/**
 * Creates iterator for notifications based on event
 */
class Factory
{
    /**
     * Creates iterator
     *
     * @param Event $event
     * @return Iterator
     * @throws Exception
     */
    public static function factory(Event $event)
    {
        $type = strtolower($event->getType());
        $iterator = new Iterator($event->getAddressedTo());
        $data = $event->getData();
        if (!$event->getCreatedBy()) {
            $iterator->setNotificationChannel('notifications.system');
        }

        switch ($type) {
            case (new AddAssigneeEvent())->getName():
                $lastAssigneeUser = $data['last_assignee'];
                if (null === $lastAssigneeUser) {
                    $lastAssignee = "не определено";
                } else {
                    $lastAssignee = self::getUserName(
                        $lastAssigneeUser['first_name'],
                        $lastAssigneeUser['last_name'],
                        $lastAssigneeUser['middle_name']
                    );
                }
                $iterator->setNotificationType(Notification::TYPE_ACTION);
                $data['action'] = [
                    'buttons' => [// available buttons
                        ConfirmAssigneeEvent::ACTION_ACCEPT,
                        ConfirmAssigneeEvent::ACTION_CANCEL
                    ],
                    'data' => [// data for buttons
                        'entity' => EventFactory::ENTITY_PROJECT,
                        'data'   => [
                            'id' => $data['project_id']
                        ]
                    ]
                ];
                $iterator->setMessage(
                    sprintf("Руководитель проекта. Назначение\n \"%s\" ← %s",
                        self::getUserName(
                            $data['assignee']['first_name'],
                            $data['assignee']['last_name'],
                            $data['assignee']['middle_name']
                        ),
                        $lastAssignee
                    )
                );
                break;
            case (new DueDateChangeEvent())->getName():
                $iterator->setMessage(
                    sprintf("%s: %s", $event->getName(), $event->getData()['issue']['name'])
                );
                break;
            case (new ReturnInWorkEvent())->getName():
                $message = sprintf("%s.\nЗадача №%d %s.\n",
                    $data['project']['name'], $data['issue']['id'], $data['issue']['name']
                );
                $message .= sprintf("Возврат в работу. Причина: %s", $data['reason']);
                $iterator->setMessage($message);
                break;
            default:
                throw new Exception(sprintf("Unknown event type for notification %s", $type));
        }
        $iterator->setExtra($data);
        return $iterator;
    }

    /**
     * Gets user name
     *
     * @param $firstName
     * @param $lastName
     * @param $middleName
     * @return string
     */
    protected static function getUserName($firstName, $lastName, $middleName)
    {
        if (!empty($middleName)) {
            $s = sprintf("%s %s %s", $lastName, $firstName, $middleName);
        } else {
            $s = sprintf("%s %s", $firstName, $lastName);
        }

        $s = preg_replace("/\s{2,}/", " ", $s);
        return trim($s);
    }
}
