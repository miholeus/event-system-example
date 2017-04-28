<?php
/**
 * @package    TaskBundle\Event\Note
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Note;

use TaskBundle\Event\EventInChain;

/**
 * Abstract project event
 * Prefixed with its own namespace
 */
abstract class AbstractEvent extends EventInChain
{
    public function getPrefix()
    {
        return 'note';
    }
}
