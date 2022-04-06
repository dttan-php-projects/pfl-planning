<?php 
	require("Database.php");
	if(isset($_GET["PAGE"]) && !isset($_GET["OPEN"])) {
		$Pages = $_GET["PAGE"];
		$row = MiQueryScalar("SELECT REDIRECT FROM au_avery.intranet_menu_2 WHERE CODEPAGE = '$Pages' LIMIT 1");
		if($row != "") {
			header('Location: /auto/planning/f1/' . $row);
			$Turn = 1;
		}
		if($Turn == 0) header('Location: /Index.php');
	} else if(isset($_GET["PAGE"]) && !isset($_GET["OPEN"])) {
		$Pages = $_GET["PAGE"];
		$row = MiQueryScalar("SELECT REDIRECT FROM au_avery.intranet_menu_2 WHERE CODEPAGE = '$Pages' LIMIT 1");
		if($row != "") {
			header('Location: /auto/planning/f1/' . $row);
			$Turn = 1;
		}
		if($Turn == 0) header('Location: /Index.php');
	}
?>