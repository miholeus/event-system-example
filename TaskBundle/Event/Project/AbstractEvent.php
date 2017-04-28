<?php
/**
 * @package    TaskBundle\Event\Project
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Project;

use TaskBundle\Event\EventInChain;

/**
 * Abstract project event
 * Prefixed with its own namespace
 */
abstract class AbstractEvent extends EventInChain
{
    public function getPrefix()
    {
        return 'project';
    }
}