<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

// get a url or form param
// note: case sensitive
function Req($param, $default = null) {
    $value = $default;

    $options = array( 'options' => array('default'=> $default) ); 
    if (filter_has_var(INPUT_GET, $param)) {
        $value = filter_input(INPUT_GET, $param, FILTER_SANITIZE_STRING, $options); 
    }
    elseif (filter_has_var(INPUT_POST, $param)) {
        $value = filter_input(INPUT_POST, $param, FILTER_SANITIZE_STRING, $options); 
    }

    return $value;
}

function q($s) {
    $t = str_replace('"', "'", $s);
    return '"' . $t . '"';
}

function sq($s) {
    return "'" . $s . "'";
}

function td($s, $class = "") {
    if (empty($class)) {
        return "<td>" . $s . "</td>";
    }
    else {
        return '<td class="' . $class . '"' . ">$s</td>";
    }
}

/**
 * create a html dt tag with $s inside the brackets 
 *  
 * @assert ("test") == "<dt>test</dt>"
 * @param type $s
 * @return string
 */
function dt($s) {
    return "<dt>" . $s . "</dt>";
}

function dd($s) {
    return "<dd>" . $s . "</dd>";
}

function img($s, $width) {
    return "<img width=" . q($width) . " src=" . q($s) . "/>";
}

function anchor($link, $text, $className) {
    return "<a class=" . q($className) . " href=" . q($link) . ">" . $text . "</a>";
}

function fileExists($f) {
    $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
    return file_exists($root . $f);
}

function nvl($val, $replace = "") {
    if (is_null($val) || $val === '') {
        return $replace;
    }
    else {
        return $val;
    }
}

function getImagePath($rs) {
    assert(!empty($rs));
    if (!isEmpty(nvl($rs->photo))) {
        $strFilePath = nvl($rs->photo);
    }
    else {
        $strFilePath = nvl($rs->student_id) . ".jpg";
    }

    $strFilePath = "/students/" . $strFilePath;
    if (!file_exists(Config::root() . $strFilePath)) {
        $strImagePath = "/students/avatar.gif";
    }
    else {
        $strImagePath = str_replace('\\', '/', $strFilePath);
    }
    return $strImagePath;
}

function getHTML($strUrl) {
    return file_get_contents($strUrl);
}

function WriteToFile($filename, $contents, $append) {
    if ($append) {
        file_put_contents($filename, $contents, FILE_APPEND);
    }
    else {
        file_put_contents($filename, $contents);
    }
}

function MapPath($url) {
    $path = parse_url($url, PHP_URL_PATH);
    //To get the dir, use: dirname($path)
    // echo $_SERVER['DOCUMENT_ROOT'] . $path;
    return dirname($path);
}

function isEmpty($s) {
    return empty($s);
}

function now() {
    return new DateTime;
}
function formatDateTime($d) {
    return $d->format('Y-m-d H:i:s');
}
?>
