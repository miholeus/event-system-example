<?php

namespace TaskBundle\Event\Listener;


use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use TaskBundle\Entity\User;
use TaskBundle\Entity\UserContact;

class UserListener
{
    public function onFlush(OnFlushEventArgs $event)
    {
        /** @var UnitOfWork $unitOfWork */
        $unitOfWork = $event->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof User) {
                $changeset = $unitOfWork->getEntityChangeSet($entity);
                $keys = array_keys($changeset);
                # reset position and/or department on organization edit when they are not changed
                if (in_array('organization', $keys)) {
                    if (!in_array('position', $keys)) {
                        $entity->setPosition(null);
                    }
                    if (!in_array('department', $keys)) {
                        $entity->setDepartment(null);
                    }
//                    $unitOfWork->computeChangeSet(
//                        $event->getEntityManager()->getClassMetadata(UserContact::class),
//                        $entity
//                    );
                }
            }
        }
    }
}
