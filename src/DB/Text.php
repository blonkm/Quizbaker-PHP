<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * some useful text functions
 */
class Text {

    /**
     * create plural version of a noun
     * 
     * @assert ('apple', 0) == 'no apples'
     * @assert ('apple', 1) == '1 apple'
     * @assert ('apple', 2) == '2 apples'
     *
     */
    function plural($s, $count) {
        if (!is_numeric($count)) {
            throw new InvalidArgumentException('count should be numeric');
        }

        $ret = $count == 0 ? 'no' : $count;
        $ret .= ' ' . $s;
        if ($count != 1)
            $ret .= 's';
        return $ret;
    }

    /**
     * convert a timestamp to a SQL compatible formatted string
     * 
     * @assert (null) throws InvalidArgumentException 
     * @assert (new DateTime("2000-07-01 14:30")) == '01-07-2000 14:30'
     * 
     * @param datetime $datetime
     * @return string
     */
    function formatDateTime($datetime) {
        // if (!PHP_SAPI) assert(is_numeric($datetime));
        if (!is_numeric($datetime)) {
            throw new InvalidArgumentException('datetime should be numeric');
        }
        return strftime('%d-%b-%Y %H:%M', $datetime);
    }

}

?>