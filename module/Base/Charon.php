<?php

namespace Base;

use JetBrains\PhpStorm\NoReturn;

class Charon
{

    /**
     * Let Charon carry the soul of our broken application across the rivers ...
     *
     * @param $subject
     * @param $cause
     * @param $coin
     */
    #[NoReturn]
    public static function carry($subject, $cause, $coin)
    {
        ob_clean();

        header('HTTP/1.1 500 Internal Server Error');

        if ($coin == 1) {

            $causeFile = sprintf('module/Base/view/error/setup/%s.html',
                $cause);

            if (is_readable($causeFile)) {

                include $causeFile;

            } else {
                echo 'Error-ception';
            }
        }

        exit();
    }

}