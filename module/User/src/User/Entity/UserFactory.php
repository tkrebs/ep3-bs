<?php

namespace User\Entity;

use Base\Entity\AbstractEntityFactory;

class UserFactory extends AbstractEntityFactory
{

    protected static $entityClass = 'User\Entity\User';
    protected static $entityPrimary = 'uid';

}