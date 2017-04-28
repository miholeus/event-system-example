<?php
/**
 * @package    TaskBundle\Event\Notification\Strategy
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace TaskBundle\Event\Notification\Strategy;

/**
 * Interface DestinationStrategyInterface
 * Determines list of users that will receive notifications in future
 */
interface DestinationStrategyInterface
{
    /**
     * Users identifiers to deliver further notifications
     *
     * @return array
     */
    public function getDestinationUsers();
}
