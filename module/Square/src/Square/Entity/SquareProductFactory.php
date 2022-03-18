<?php

namespace Square\Entity;

use Base\Entity\AbstractEntityFactory;

class SquareProductFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'Square\Entity\SquareProduct';
    protected static $entityPrimary = 'spid';

}