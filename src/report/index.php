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

function showLinks() {
    // connect
    $db = new Database(Config::$dbname);
    $db->connect();

    $rs = $db->query("SELECT * FROM quiz ORDER BY quiz_name");
    ?>
    <p><a href="showScores.php">Download scores archive</a></p>
    <p><a href="showScores.php?details=true">Download details archive</a></p>
    <table>
        <tr>
            <th>name</th>
            <th>details</th>
            <?php foreach ($rs as $row) { ?>
            <tr>
                <td><a href="showScores.php?view=true&amp;id=<?= $row->id ?>"><?=$row->quiz_name?></a></td>
                <!-- <td><a href="showScores.php?view=true&amp;id=<?= $row->id ?>">view</a>
                    <a href="showScores.php?id=<?= $row->id ?>">download</a></td> -->
                <td><a href="showScores.php?details=true&id=<?= $row->id ?>">download</a></td></tr><?
        }
        ?></table><?php
            }

            function showClass() {
                // connect
                $db = new Database(Config::$dbname);
                $db->connect();

                $sql = "SELECT DISTINCT department, class FROM vwsummary WHERE class IS NOT NULL ORDER BY Department, Class";
                $rs = $db->query($sql);
                ?>
    <table>
        <tr>
            <th>Department</th>
            <th>Class</th>
            <?php foreach ($rs as $row) { ?>
            <tr><td><?= $row->department ?></td>
                <td><a href="showScores.php?view=true&style=list&class=<?= $row->class ?>"><?= $row->class ?></a></td></tr>
        <?php } ?>
    </table>
<?php } ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Quizes</title>
        <style>
            body { font: .76em arial; margin:1em; }
            td, th {border:1px solid silver; padding:.2em}
            table {border-collapse:collapse}
            tr:nth-child(odd) { background-color: #e2f7f7;}
        </style>
    </head>
    <body>
        <img src="/DB/logo.png" width="200"/>
        <h1>Quizes</h1>
        <?php showLinks(); ?>
        <p>&nbsp;</p>
        <?php showClass(); ?>

    </body>
</html>