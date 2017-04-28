<?php
/**
 * @package    TaskBundle\Event\Note
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Note;


class AddNoteEvent extends AbstractEvent
{
    protected $name = 'add';

    public function getDescription()
    {
        return 'Добавление заметки';
    }

}
