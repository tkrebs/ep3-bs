<?php

namespace Base\View\Helper\Layout;

use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;

class ShortUrl extends AbstractHelper
{

    public function __invoke($url)
    {
        $url = UriFactory::factory($url);

        return $url->getHost();
    }

}
