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

$rs = null;

function search($s) {
    $sortField = "id";
    if (!isEmpty(Req("sort")))
        $sortField = Req("sort");
    if (!isEmpty($s)) {
        $db = new Database(Config::$dbname);
        $sql = "SELECT * FROM students ";
        $sql .= "WHERE first_name LIKE @q ";
        $sql .= "OR last_name LIKE @q ";
        $sql .= "OR class LIKE @q ";
        $sql .= "OR id LIKE @q ";
        $sql .= "ORDER BY " . $sortField;
        $sql = str_replace("@q", sq("%" . $s . "%"), $sql);
        $rs = $db->getRs($sql);
        return $rs;
    }
    else
        return false;
}

function imageLink($id, $size) {
    $strFilePath = $id . ".jpg";
    $strImgPath = "/students/" . $strFilePath;
    if (!fileExists($strImgPath))
        $strImgPath = "/students/avatar.gif";
    return anchor($strImgPath, img($strImgPath, $size), "lightbox");
}

$sp = Req("showpictures");
if (empty($sp))
    $sp = "false";

$term = trim(Req("q"));
$rs = search($term);
if ($rs) {
    $count = $rs->rowCount();
    // print (imageLink($rs->id));
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>QuizBaker Student Search Engine</title>
        <style>
            #main { text-align:center; margin: 0 auto; display: block; width: 40em; margin-top: 150px; }
            #searchForm button,	#searchForm input { font-size:2em; }
            #searchForm img { margin-bottom:0px; margin-right:8px; }
            body { font: .76em arial; margin:1em; }
            td, th { border:1px solid silver; padding:.2em }
            th { cursor:pointer;}
            table { border-collapse:collapse; margin:1em auto; width:440px;	margin-bottom:2em; }
            tr:nth-child(odd) { background-color: #e2f7f7; }
            dl { margin: 0;	padding: 0; }
            dt { margin: 0;  padding: 0; font-weight: bold; }
            dd { margin: 0 0 1em 0;	padding: 0; }
        </style>
        <script type="text/javascript">
            function togglePictures() {
                var sp = document.getElementById("showpictures");
                sp.value = !(String(true) == sp.value);
                sp.form.submit();
            }
            function setSort(f) {
                var sortField = document.getElementById("sort");
                sortField.value = f;
                sortField.form.submit();
            }
        </script>
    </head>

    <body>
        <div id="main">
            <h1><a href="/students/index.asp"><img src="Schoogle.gif" width="349" height="110" alt="Schoogle" /></a></h1>
            <form id="searchForm" method="get">
                <img title="toggle student pictures" class="sp" src="avatar<?= $sp ? "-on" : "" ?>.gif" width="40" height="30" alt="toggle student pictures" onclick="togglePictures()" />
                <input type="text" value="<?= $term ?>" id="q" name="q" />
                <input type="hidden" value="<?= $sp ?>" id="showpictures" name="showpictures" />
                <input type="hidden" value="id" id="sort" name="sort" />
                <button type="submit" id="submitbutton" >Search</button>
            </form>
            <div id="results">
                <? if ($rs) { ?>

                    <p><?= $rs->rowCount() ?> student records found</p>
                    <table>
                        <tr>
                            <?
                            if (Req("showpictures") == "true") {
                                ?><th>Foto</th><?
                            }
                            ?>
                            <th title="sort by student id" onclick="setSort('id')">Student Id</th>
                            <th title="sort by last name" onclick="setSort('last_name')">Last Name</th>
                            <th title="sort by first name" onclick="setSort('first_name')">First Name</th>
                            <th title="sort by class" onclick="setSort('class')">Class</th>
                        </tr>
                        <?
                        foreach ($rs as $row) {
                            ?>
                            <tr>
                                <?
                                if (Req("showpictures") == "true") {
                                    print td(imageLink($row->id, 60));
                                }
                                ?>
                                <td><a href="/report/showStudent.asp?id=<?= $row->id ?>"><?= strtoupper($row->id) ?></td>
                                <?= td($row->last_name) ?>
                                <?= td($row->first_name) ?>
                                <?= strtoupper(td($row->class)) ?></tr><?
                        }
                        ?></table><?
                    }
                    ?>
            </div>
        </div>
    </body>
</html>