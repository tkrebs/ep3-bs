<?php

$extraModules = [];

foreach (glob(getcwd() . '/modulex/*/') as $extraModule) {

    $extraModuleName = basename($extraModule);

    if (! str_starts_with($extraModuleName, '!')) {

        $extraModules[] = $extraModuleName;
    }
}

return $extraModules;
