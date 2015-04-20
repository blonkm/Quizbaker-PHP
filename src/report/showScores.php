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

$c = ",";
$nl = "\n";
$br = "<br/>";
$oQuiz = new Quiz();

// Main
$action = Req("action");
switch (strtolower($action)) {
    case "delete":
        deleteQuizResult(intval(Req("id")));
        break;
    case "export":
        exportResults(Req("class"));
        break;
    case "history":
        $days = Req("n");
        if (empty($days)) {
            $days = 1;
        }
        $quizVisible = "quizVisible";
        break;
    default:
        if (isEmpty(Req("view"))) {
            if (Req("details") === "true") {
                saveDetails();
            } else {
                saveScores();
            }
        }
}

// Routines
function showHistory($days) {
    global $oQuiz;
    // init db connection
    $db = $oQuiz->getDB();

    // get scores
    $sql = "SELECT * FROM vwsummary";
    $sql .= " WHERE lastmodified >= DATE_ADD(day, -" . $days . ", GETDATE())";
    $sql .= " ORDER BY lastmodified DESC";

    $rs = $db->getRs(sql);
    showScoresList($rs);
}

function deleteQuizResult($id) {
    global $oQuiz;

    // init db connection
    $db = $oQuiz->getDB();

    // get details
    $sql = "DELETE FROM quiz_summary WHERE ID=" . $id;
    $ret1 = $db->execute($sql);
    $sql = "DELETE FROM quiz_detail WHERE summary_id=" . $id;
    $ret2 = $db->execute($sql);
    return $ret1 && $ret2;
}

function displayDetails() {
    global $oQuiz, $nl;
    // init db connection
    $db = $oQuiz->getDB();
    $quizName = $oQuiz->GetName(Req("id"));

    // get details
    $sql = "SELECT * FROM vwdetails ";
    $sql .= "WHERE quiz_id=" . intval(Req("id")) . " ";
    if (!isEmpty(Req("student"))) {
        $sql .= "AND student_id =" . sq(Req("student")) . " ";
    }

    $sql .= "ORDER BY timestamp DESC";
    $rs = $db->getRs($sql);
    $row = $rs->fetch();
    $strImagePath = getImagePath($row);
    ?>
    <h2>Scores voor quiz: <strong><?= $quizName ?></strong></h2>
    <?= anchor($strImagePath, img($strImagePath, 120), "lightbox") ?>

    <dl>
        <dt>ID</dt><dd><?= $row->id ?></dd>
        <dt>Student</dt><dd><?= $row->last_name ?>, <?= $row->first_name ?></dd>
        <dt>Class</dt><dd><?= $row->class ?></dd>
        <dt>Percentage</dt><dd><?= $row->raw_score ?>%</dd>
    </dl>
    <table>
        <tr>
            <th>questionNum</th>
            <th>question</th>
            <th>response</th>
            <th>result</th>
            <th>score</th>
            <th>type</th>
        </tr>
        <?
        foreach ($rs as $row) {
            print "<tr>";
            print td($row->question_num) . $nl;
            print td($row->question) . $nl;
            print td($row->student_response) . $nl;
            print td($row->result) . $nl;
            print td($row->score) . $nl;
            print td($row->interaction_type) . $nl;
            print "</tr>";
        }
        ?></table><?
}

function exportResults($class) {
    global $oQuiz;

    // init db connection
    $db = $oQuiz->getDB();

    // get scores
    $sql = "SELECT * FROM vwsummary";
    if (!empty($class)) {
        $sql .= " WHERE class=" . sq($class);
        $sql .= " ORDER BY class, last_name";
    } else {
        $sql .= " WHERE quiz_id=" . intval(Req("id"));
        $sql .= " ORDER BY lastmodified DESC";
    }

    $curdir = MapPath("/students/");
    $rs = $db->getRs(sql);
    foreach ($rs as $row) {
        $url = "http://" . Svr("SERVER_NAME") . "/report/showScores.php?view=true&details=true&id=" . $row->quiz_id . "&student=" . $row->student_id;
        $html = getHtml($url);
        writeToFile($curdir . '\\output\\' . $row->student_id . ".html", $html, false);
    }
}

function displayScores($class) {
    global $oQuiz;
    // init db connection
    $db = $oQuiz->getDB();

    if (!isEmpty(Req("id"))) {
        $quizName = $oQuiz->getName(Req("id"));
    }

    // get scores
    $sql = "SELECT * FROM vwsummary";
    if (!empty($class)) {
        $sql .= " WHERE current_class=" . sq($class);
        $sql .= " ORDER BY class, last_name";
    } else {
        $sql .= " WHERE quiz_id=" . Req("id");
        $sql .= " ORDER BY lastmodified DESC";
    }
    $rs = $db->getRs($sql);

    if (!isEmpty(Req("id"))) {
        ?><h2>Scores for quiz: <strong><?= $quizName ?></strong></h2><?
        } elseif (!isEmpty(Req("class"))) {
            ?><h2>Scores for class: <strong><?= Req("class") ?></strong></h2><?
    }

    if (!empty($class)) {
        ShowScoresClass($rs);
    } else {
        if (strtolower(Req("style")) === "pictures") {
            ShowScoresPictures($rs);
        } else {
            ShowScoresList($rs);
        }
    }
}

function ShowScoresClass($rs) {
    global $nl;

    $studentId = Req("studentId");
    foreach ($rs as $row) {
        if ($studentId != $row->student_id && !empty($studentId)) {
            ?></table><?php
        }
        if ($studentId != $row->student_id) {
            ?><h2><?= $row->last_name ?>, <?= $row->first_name ?></h2><?
            $studentId = $row->student_id;
            $strImagePath = getImagePath($row);
            ?><?= anchor($strImagePath, img($strImagePath, 120), "lightbox") ?>

            <dl>
                <dt>ID</dt><dd><a href="showStudent.php?id=<? print $row->student_id ?>"><?= $row->student_id ?><a/></dd>
                <dt>Student</dt><dd><?= $row->last_name ?>, <?= $row->first_name ?></dd>
                <dt>Class</dt><dd><?= $row->current_class ?></dd>
            </dl>
            <table>
                <tr>
                    <th>quiz</th>
                    <th>status</th>
                    <th>score</th>
                    <th>grade</th>
                    <th>time</th>
                    <th>view</th>
                </tr>
            <?
        }

        print("<tr>");
        print(td(nvl($row->quiz_name)) . $nl);
        print(td($row->status) . $nl);
        print(td($row->raw_score) . $nl);
        print(td(round($row->raw_score / 100 * 9 + 1, 1)) . $nl);
        print(td($row->time));
        ?><td><a href = "showScores.php?view=true&details=true&id=<?= $row->quiz_id ?>&student=<?= $row->student_id ?>">view<a/></td><?
            print("\n");
            print("</tr>");
        }
    }

    function ShowScoresList($rs) {
        global $nl, $quizVisible;
        ?>
        <table class="<?= Req("style") ?>">
            <tr>
                <th class = "<?= $quizVisible ?>">quiz</th>
                <th class="photo">photo</th>
                <th>first name</th>
                <th>last name</th>
                <th>class</th>
                <th>student</th>
                <th>status</th>
                <th>score</th>
                <th>grade</th>
                <th>time</th>
                <th>view</th>
                <th>delete</th>
            </tr>
    <?
    foreach ($rs as $row) {
        $strImagePath = getImagePath($row);

        print("<tr>");
        ?><td class = "<?= $quizVisible ?>"><?= $row->quiz_name ?></td><?
                print(td(anchor($strImagePath, img($strImagePath, 60), "lightbox "), 'photo') . $nl);
                print(td($row->first_name) . $nl);
                print(td($row->last_name) . $nl);
                print(td($row->current_class) . $nl);
                ?><td><a href="showStudent.php?id=<?= $row->student_id ?>"><?= $row->student_id ?><a/></td><?
                print(td($row->status) . $nl);
                print(td($row->raw_score) . $nl);
                print(td(round($row->raw_score / 100 * 9 + 1, 1)) . $nl);
                print(td($row->time));
                ?><td><a href="showScores.php?view=true&details=true&id=<?= $row->quiz_id ?>&student=<?= $row->student_id ?>">view<a/></td><?
                ?><td><a onclick="return confirm('You are about to delete id <?= $row->id ?> for <?= $row->student_id ?>. Continue?');" href="showScores.php?action=delete&id=<?= $row->id ?>&student=<?= $row->student_id ?>">delete<a/></td><?
                    print("\n");
                    print("</tr>");
                }
                ?></table><?
        }

        function showScoresPictures($rs) {
            global $nl, $br;
            ?>
        <div id="photobook">
        <?
        foreach ($rs as $row) {
            $strImagePath = getImagePath($row);
            ?><div><?
                print(anchor($strImagePath, img($strImagePath, 120), "lightbox") . $nl);
                ?><dl class="tooltip"><?
                    print(dt($row->quiz_id) . $nl);
                    print(dt("First name") . dd($row->first_name) . $nl);
                    print(dt("Last name") . dd($row->last_name) . $nl);
                    print(dt("Class") . dd($row->class) . $nl);
                    print(dt("Student") . dd($row->student_id) . $nl);
                    print(dt("Status") . dd($row->status) . $nl);
                    print(dt("Score") . dd($row->raw_score) . $nl);
                    print(dt("Grade") . dd(round($row->raw_score / 100 * 9 + 1, 1)) . $nl);

                    $strTitle = $row->first_name . " " . $row->last_name . $br;
                    $strTitle .= "Class: " . $row->current_class . $br;
                    $strTitle .= "ID: " . $row->student_id . $br;
                    $strTitle .= "Status: " . $row->status . $br;
                    $strTitle .= "Score: " . $row->raw_score . $br;
                    ?></dl>

                    <p><a href="showScores.php?view=true&details=true&id=<?= $row->quiz_id ?>&student=<?= $row->student_id ?>">details<span class="tooltip"><span></span><?= $strTitle ?></span><a/></p><?
                print("\n");
                print("</div>");
            }
                ?></table><?
            }

            function saveScores() {
                global $oQuiz, $c;

                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="scores.csv"');

                $db = $oQuiz->getDB();

                $sql = "SELECT * FROM vwsummary ";
                if (!isEmpty(Req("id"))) {
                    $sql .= " WHERE quiz_id = " . Req("id");
                }
                $sql .= " ORDER BY lastmodified DESC";
                $rs = $db->getRs($sql);
                print("id, quizname, firstname, lastname, class, student, status, score, time\n");
                foreach ($rs as $row) {
                    print(q($row->quiz_id) . $c);
                    print(q(nvl($row->quiz_name)) . $c);
                    print(q(nvl($row->first_name)) . $c);
                    print(q(nvl($row->last_name)) . $c);
                    print(q(nvl($row->current_class)) . $c);
                    print(q($row->student_id) . $c);
                    print(q(nvl($row->status)) . $c);
                    print(q($row->raw_score) . $c);
                    print(q($row->time));
                    print("\n");
                }
                exit(); // terminate page processing. EOF
            }

            function saveDetails() {
                global $oQuiz, $c;

                header('Content-type: text/csv');
                header('Content-Disposition: attachment; filename="details.csv"');
                $db = $oQuiz->getDB();

                $sql = "SELECT * FROM vwdetails ";
                if (!isEmpty(Req("id"))) {
                    $sql .= " WHERE quiz_id = " . intval(Req("id"));
                }
                $sql .= " ORDER BY timestamp DESC";
                $rs = $db->getRs($sql);
                print "quizid, quizname, student, question_number, question, response, result, score, percentage, type, last, first, class\n";
                foreach ($rs as $row) {
                    print(q($row->quiz_id) . $c);
                    print(q($row->quiz_name) . $c);
                    print(q($row->student_id) . $c);
                    print(q($row->question_num) . $c);
                    print(q($row->question) . $c);
                    print(q("'" . $row->student_response) . $c);
                    print(q($row->result) . $c);
                    print(q($row->score) . $c);
                    print(q($row->raw_score) . $c);
                    print(q($row->interaction_type) . $c);
                    print(q($row->last_name) . $c);
                    print(q($row->first_name) . $c);
                    print(q(nvl($row->current_class)));
                    print("\n");
                }
                exit();
            }

            function cleanup() {
                global $oQuiz;

                $db = $oQuiz->getDB();

                $sql = "SELECT * FROM quiz_summary WHERE status = ''";
                $rs = $db->getRs($sql);
                foreach ($rs as $row) {
                    $sql = "DELETE FROM quiz_detail WHERE summary_id = " . intval($row->id);
                    $db->execute($sql);
                    $sql = "DELETE FROM quiz_dummary WHERE id = " . intval($row->id);
                    $db . execute($sql);
                }
                $sqlDelete = "DELETE FROM quiz WHERE quiz_name = ''";
                $db->execute($sqlDelete);
            }
            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Results</title>
                    <link rel = "stylesheet" type = "text/css" href = "/report/lightbox/css/jquery.lightbox-0.5.css" media = "screen" />
                    <link rel = "stylesheet" type = "text/css" href = "tooltips.css" media = "screen" />
                    <link rel = "stylesheet" type = "text/css" href = "report.css" media = "screen" />

                    <script type = "text/javascript" src = "/DB/jquery.min.js"></script>
                    <script type="text/javascript" src="/report/lightbox/js/jquery.lightbox-0.5.js"></script>

                    <script type="text/javascript">
                    $(function () {
                        $('a.lightbox').lightBox();
                    });
                    </script>
                </head>
                <body>
                    <p id="credits"><a href="http://about.me/michiel">Help<span class="tooltip"><span></span>Vragen? Email Michiel van der Blonk</span></a></p>
                    <a href="/report/"><img src="/DB/logo.png" width="200"/></a>
                    <h1>Results
<?
if (!empty($days)) {
    if ($days == 1) {
        print (" vandaag");
    }
    if ($days > 1) {
        print (" laatste " . $days . " dagen");
    }
}
?>
                    </h1>
                        <?
                        cleanup();

                        if (Req("action") == "delete") {
                            ?>
                        <p>Record <?= Req("id") ?> for student <a href="showStudent.php?id=<?= Req("student") ?>"><?= Req("student") ?><a/> has been deleted.</p>
                        <?
                    } else {
                        if (Req("view") == "true") {
                            if (Req("class") != "") {
                                displayScores(Req("class"));
                            } else {
                                if (Req("details") === "true") {
                                    displayDetails();
                                } else {
                                    ?>
                                    <form name="style" method="get">
                                        <input type="hidden" name="view" value="<?= Req("view") ?>" />
                                        <input type="hidden" name="id" value="<?= Req("id") ?>" />
                                        <button type="submit" name="style" value="pictures">Pictures</button>
                                        <button type="submit" name="style" value="list">List</button>
                                        <button type="submit" name="action" value="export">Export</button>
                                    </form>
                <?
                displayScores("");
            }
        }
    } else {
        if (action == "history") {
            showHistory(days);
        }
    }
}
?>

                </body>
            </html>
