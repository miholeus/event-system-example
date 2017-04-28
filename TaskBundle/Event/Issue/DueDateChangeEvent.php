<?php
/**
 * @package    TaskBundle\Event\Issue
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */


namespace TaskBundle\Event\Issue;

/**
 * Issue change due date event
 */
class DueDateChangeEvent extends AbstractEvent
{
    protected $name = 'update.due_date';

    public function getDescription()
    {
        return 'Изменился срок по задаче';
    }
}
