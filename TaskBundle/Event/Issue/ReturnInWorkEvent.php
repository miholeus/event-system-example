<?php
/**
 * @package    TaskBundle\Event\Issue
 * @version    $Id: $
 */

namespace TaskBundle\Event\Issue;

/**
 * Return issue in work event
 */
class ReturnInWorkEvent extends AbstractEvent
{
    protected $name = 'return.in_work';

    public function getDescription()
    {
        return 'Возврат завершенной задачи в работу';
    }
}
