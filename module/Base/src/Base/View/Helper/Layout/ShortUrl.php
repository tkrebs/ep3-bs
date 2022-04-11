<?php

namespace Base\View\Helper\Layout;

use Zend\Uri\UriFactory;
use Zend\View\Helper\AbstractHelper;

class ShortUrl extends AbstractHelper
{

    public function __invoke($url = null)
    {
        if  ( $ret = parse_url($url) ) {

            if ( !isset($ret["scheme"]) )
             {
             $url = "https://{$url}";
             }
        }        
        if ($url) {
            $url = UriFactory::factory($url);

            return $url->getHost();
        } else {
            return $url;
        }
    }

}
