<?php
/**
 * @package    TaskBundle\Event\Processor
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Processor;

interface ProcessorInterface
{
    /**
     * Process event
     *
     * @param mixed $event
     */
    public function process($event);
}
