<?php

namespace Event\Entity;

use Base\Entity\AbstractLocaleEntityFactory;

class EventFactory extends AbstractLocaleEntityFactory
{

    protected static $entityClass = 'Event\Entity\Event';
    protected static $entityPrimary = 'eid';

}