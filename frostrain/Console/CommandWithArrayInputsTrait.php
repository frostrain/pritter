<?php

namespace Frostrain\Console;

trait CommandWithArrayInputsTrait
{
    /**
     * @param str $input
     * @param str $seperator
     * @return array
     */
    protected function parseArrayInput($input, $seperator = ',')
    {
        if (strstr($input, $seperator)) {
            $r = explode($seperator, $input);
        } else {
            if ($input) {
                $r = [$input];
            } else {
                $r = [];
            }
        }
        return $r;
    }
}
