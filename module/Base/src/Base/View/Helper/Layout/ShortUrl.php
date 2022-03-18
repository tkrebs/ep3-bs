<?php

namespace Base\View\Helper\Layout;

use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;

class ShortUrl extends AbstractHelper
{

    public function __invoke($url = null)
    {
        if ($url) {
            $url = UriFactory::factory($url);

            return $url->getHost();
        } else {
            return $url;
        }
    }

}
