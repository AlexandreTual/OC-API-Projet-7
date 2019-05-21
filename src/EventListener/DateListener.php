<?php

namespace App\EventListener;

use App\Entity\Behavior\TimestampableTrait;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DateListener
{
    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->issetTrait($entity)) {
            $entity->setCreatedAt(new \DateTime());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($this->issetTrait($entity)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    private function issetTrait($entity): Bool
    {
        $traits = class_uses($entity);
        foreach ($traits as $trait) {
            if (TimestampableTrait::class == $trait) {
                return true;
            }
        }

        return false;
    }
}
