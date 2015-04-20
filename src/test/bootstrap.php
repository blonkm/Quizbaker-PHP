<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Michiel
 */
// TODO: check include path
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../DB'.PATH_SEPARATOR.dirname(__FILE__).'/..'.PATH_SEPARATOR.dirname(__FILE__).'/../report');

// put your code here
/*
 * function __autoload($class_name) {
 
    include $class_name . '.php';
}
 * 
 */

$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
require_once($root . '/DB/Config.php');
require_once($root . '/DB/Debug.php');
require_once($root . '/DB/Database.php');
require_once($root . '/DB/Text.php');
require_once($root . '/DB/QuizData.php');
require_once($root . '/DB/Quiz.php');
require_once($root . '/DB/utils.php');
?>
