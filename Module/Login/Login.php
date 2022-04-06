<?php 
	require("../Database.php");
	if (isset($_GET['USERNAME']) && isset($_GET['PASSWORD']) && $_GET['USERNAME'] != null && $_GET['PASSWORD'] != null) {
		$User = $_GET['USERNAME'];
		$Pass = $_GET['PASSWORD'];
		$Username = MiQueryScalar("SELECT USERNAME FROM intranet.intranet_user WHERE USERNAME = '$User' AND Password = SHA1('$Pass') AND ACTIVE = 1 LIMIT 1");
		if($Username == $User) {
			setcookie("ZeroIntranet", $Username, time() + (86400 * 30), "/");
			echo "OK";
		} else echo "NG";
	} 
?>