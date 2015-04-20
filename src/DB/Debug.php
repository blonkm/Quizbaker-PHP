<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

class Debug {

    var $mode;
    var $pre;
    var $level = 1;

    /**
     * 
     * @param string $s
     * @param string $HTML
     * @param boolean $insertNewlines
     * @param boolean $pre
     */
    function out($s, $HTML = false, $insertNewlines = true, $pre = false) {
        if (isset($this) && $this instanceof Debug && !$pre)
            $pre = $this->pre;
        if (isset($this) && $this instanceof Debug && !$HTML)
            $HTML = $this->mode;

        if ($pre)
            echo "<pre>";
        if ($HTML) {
            ?><div class="debug"><?=$s; ?>
            <? if ($insertNewlines) echo "<br />"; ?>
            </div><?
            } else
                print_r($s);
            if ($insertNewlines)
                echo "\n";
        }

    }
?>
