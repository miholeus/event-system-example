<?php
/**
 * @package    TaskBundle\Event\Issue
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Issue;

use TaskBundle\Event\EventInChain;

/**
 * Abstract issue event
 * Prefixed with its own namespace
 */
abstract class AbstractEvent extends EventInChain
{
    public function getPrefix()
    {
        return 'issue';
    }
}
