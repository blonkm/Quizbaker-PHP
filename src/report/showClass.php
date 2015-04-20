<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
* License, v. 2.0. If a copy of the MPL was not distributed with this file,
* You can obtain one at http://mozilla.org/MPL/2.0/. */

$root = input_filter(INPUT_SERVER, 'DOCUMENT_ROOT');
require_once($root . '/DB/Config.php');
require_once($root . '/DB/Debug.php');
require_once($root . '/DB/Database.php');
require_once($root . '/DB/Text.php');
require_once($root . '/DB/QuizData.php');
require_once($root . '/DB/Quiz.php');
require_once($root . '/DB/utils.php');

function ShowClass($class) {
        $nl = vbNewLine;
        $br = "<br/>";
        $oQuiz = new Quiz;
        
	// init db connection
	 $db = $oQuiz->getDB();

	// get scores
	$sql = "SELECT * FROM students WHERE class=" . sq($class) . " ORDER BY last_name ASC";
        // print(sql)
	$rs = $db->getRs($sql);
	if (!$rs) {
		?><p>No students found in class <?=$class?>.</p><?php
        }

	?>
	<div id="photobook">
	<?php
	foreach ($rs as $row) {
		$strImagePath = getImagePath($rs);

		?><div><?php
		print anchor($strImagePath, img($strImagePath, 120), "lightbox") . $nl
		?><dl class="tooltip"><?php
		print dt("First name") . dd($row->first_name) . $nl;
		print dt("Last name") . dd($row->last_name) . $nl;
		print dt("Class") . dd($row->class) . $nl;
		print dt("Number") . dd($row->id) . $nl;

		$strTitle = $row->first_name . " " . $row->last_name . $br;
		$strTitle .= "Class: " . $row->class . $br;
		$strTitle .= "ID: " . $row->id . $br;
		?></dl>

		<p><a href="showScores.asp?view=true&details=true&student=<?=$row->id?>"><?=$row->first_name?><br/><?=$row->last_name?><span class="tooltip"><span></span><?=$strTitle?></span><a/></p><?php
		print("\n");
		print("</div>");
        }
    ?></table><?php
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Class <?=Req("class")?></title>
    <link rel="stylesheet" type="text/css" href="/report/lightbox/css/jquery.lightbox-0.5.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="tooltips.css" media="screen,print" />
    <link rel="stylesheet" type="text/css" href="report.css" media="screen,print" />
    <script type="text/javascript" src="/DB/jquery.min.js"></script>
    <script type="text/javascript" src="/report/lightbox/js/jquery.lightbox-0.5.js"></script>

    <script>
    $(function() {
            $('a.lightbox').lightBox();
    });
    </script>
</head>
<body>
<p id="credits"><a href="http://about.me/michiel">Help<span class="tooltip"><span></span>Questions? Email Michiel van der Blonk</span></a></p>
<a href="/report/"><img id="logo" src="/DB/logo.png" width="200"/></a>
<h1>Class <?=Req("class")?></h1>
<?php
showClass (Req("class"));
?>
</body>
</html>
