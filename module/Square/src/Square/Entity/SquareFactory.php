<?php

namespace Square\Entity;

use Base\Entity\AbstractLocaleEntityFactory;

class SquareFactory extends AbstractLocaleEntityFactory
{

    protected static $entityClass = 'Square\Entity\Square';
    protected static $entityPrimary = 'sid';

}