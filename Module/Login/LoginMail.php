<?php 
	require("../Database.php");

	if (isset($_POST['MAIL']) && $_POST['MAIL'] != null && $_POST['IDPAGE'] != null) {
		$Mail = $_POST['MAIL'];
		$IDPage = $_POST['IDPAGE'];
		if(strpos($Mail,"averydennison") !== false)
		{
			setcookie("ZeroIntranet", explode("@",$Mail)[0], time() + (86400 * 30), "/");
			header('Location: ./Module/Redirect.php?PAGE=' . $IDPage);
		} else 
		{
			header('Location: ./Module/Login.php');
		}
	} 
?>