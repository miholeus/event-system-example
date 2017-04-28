<?php

namespace TaskBundle\Event\Issue;

class IssueRelatesUpdateEvent extends AbstractEvent
{
    protected $name = 'update.relation_type';

    public function getDescription()
    {
        return 'Обновление зависимостей задач';
    }
}
