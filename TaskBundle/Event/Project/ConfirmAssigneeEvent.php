<?php
/**
 * @package    TaskBundle\Event\Project
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Project;


class ConfirmAssigneeEvent extends AbstractEvent
{
    const ACTION_ACCEPT = 'accept';
    const ACTION_CANCEL = 'cancel';

    protected $name = 'confirm.assignee';

    public function getDescription()
    {
        return 'Принятие/Отклонение руководителя проекта';
    }
}
