<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/DB/Config.php');
require_once($root . '/DB/Debug.php');
require_once($root . '/DB/Database.php');
require_once($root . '/DB/Text.php');
require_once($root . '/DB/QuizData.php');
require_once($root . '/DB/Quiz.php');
require_once($root . '/DB/utils.php');


$q = new Quiz();
if ($q->save()) {
    $out = new stdClass();
    $out->quizName = $q->title;
    $out->username = $q->username;
    $out->quizId = $q->quizId;
    $out->summaryId = $q->summaryId;
    print json_encode($out);    
} 
else
    print "false";
?>