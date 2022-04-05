<?php

namespace Square\Entity;

use Base\Entity\AbstractEntityFactory;

class SquareGroupFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'Square\Entity\SquareGroup';
    protected static $entityPrimary = 'sgid';

}