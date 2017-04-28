<?php
/**
 * @package    TaskBundle\Event\Project
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Project;
/**
 * Event is triggered when new assignee is added to project
 */
class AddAssigneeEvent extends AbstractEvent
{
    protected $name = 'add.assignee';

    public function getDescription()
    {
        return 'Добавление руководителя';
    }
}
