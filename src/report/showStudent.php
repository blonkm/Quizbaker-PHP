<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
require_once($root . '/DB/Config.php');
require_once($root . '/DB/Debug.php');
require_once($root . '/DB/Database.php');
require_once($root . '/DB/Text.php');
require_once($root . '/DB/QuizData.php');
require_once($root . '/DB/Quiz.php');
require_once($root . '/DB/utils.php');

function displayScores() {
    $oQuiz = new Quiz();

    // init db connection
    $db = $oQuiz->getDB();

    // get scores
    $sql = "SELECT * FROM vwsummary WHERE student_id=" . sq(Req("id")) . " ORDER BY lastmodified DESC";
    $rs = $db->getRs($sql);
    $row = $rs->fetch();
    if (!$rs) {
        ?><p>This student has not taken any quiz yet.</p><?
    } else {
        $strImagePath = getImagePath($row);
        ?>

        <h2>Scores for student: <strong><?= Req("id") ?></strong></h2>

        <?= anchor($strImagePath, img($strImagePath, 120), "lightbox") ?>
        <dl>
            <dt>ID</dt><dd><?= $row->student_id ?></dd>
            <dt>Student</dt><dd><?= $row->last_name ?>, <?= $row->first_name ?></dd>
            <dt>Class</dt><dd><?= $row->class ?></dd>
        </dl>

        <?
        $nl = "\n";

        $oQuiz = new Quiz();
        ?>
        <table>
            <tr>
                <th>quiz</th>
                <th>status</th>
                <th>score</th>
                <th>time</th>
                <th>view</th>
                <th>delete</th>
            </tr>
            <?
            foreach ($rs as $row) {
                print("<tr>");
                print(td($oQuiz->GetName($row->quiz_id)) . $nl);
                print(td($row->status) . $nl);
                print(td($row->raw_score) . $nl);
                print(td($row->time));
                ?><td><a href="showScores.php?view=true&details=true&id=<?= $row->quiz_id ?>&student=<?= $row->student_id ?>">view<a/></td><?
                print("\n");
                ?><td><a onclick="return confirm('You are about to delete id <?= $row->id ?> for <?= $row->student_id ?>. Continue?');" href="showScores.php?action=delete&id=<? $row->id ?>&student=<?= $row->student_id ?>">delete<a/></td><?
                print("</tr>");
            }
            ?></table><?
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Resultaten <?= Req("id") ?></title>
        <link rel = "stylesheet" type = "text/css" href = "/report/lightbox/css/jquery.lightbox-0.5.css" media = "screen" />
        <link rel = "stylesheet" type = "text/css" href = "tooltips.css" media = "screen" />
        <link rel = "stylesheet" type = "text/css" href = "report.css" media = "screen" />

        <script type = "text/javascript" src = "/DB/jquery.min.js"></script>
        <script type="text/javascript" src="/report/lightbox/js/jquery.lightbox-0.5.js"></script>

        <script>
        $(function() {
        $('a.lightbox').lightBox();
        });
        </script>
    </head>
    <body>
        <p id="credits"><a href="http://about.me/michiel">Help<span class="tooltip"><span></span>Vragen? Email Michiel van der Blonk</span></a></p>
        <a href="/report/"><img src="/DB/logo.png" width="200"/></a>
        <h1>Resultaten</h1>
        <?
        displayScores();
        ?>

    </body>
</html>
