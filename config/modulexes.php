<?php

$extraModules = array();

foreach (glob(getcwd() . '/modulex/*/') as $extraModule) {

    $extraModuleName = basename($extraModule);

    if (strpos($extraModuleName, '!') !== 0) {

        $extraModules[] = $extraModuleName;
    }
}

return $extraModules;
