<?php
	// database
	include_once ("Module/Database.php");
	// get User Login
	function getUser() 
	{
		$username = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
		if (!empty($username) ) {
			$username = isset($_COOKIE["VNRISIntranet"]) ? $_COOKIE["VNRISIntranet"] : "";
		}

		return $username;
	}

	function getAutomailLog() 
	{
		$res = MiQuery( "SELECT `CREATEDDATE` FROM au_avery.autoload_log WHERE `FUNC`='AUTOMAIL' ORDER BY `ID` DESC LIMIT 1;",_conn1());
		return !empty($res[0]['CREATEDDATE']) ? $res[0]['CREATEDDATE'] : 'loading...' ;
	}


	// functions
		include_once ("jsfunction.php");

	// html string
		$html = '<!DOCTYPE html>
				<html>
					<head>
						<!-- meta block -->
						<title>PFL Jobjacket</title>
						<meta name="description" content="Planning">
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
						<link rel="icon" href="./Module/Images/Logo.ico" type="image/x-icon">
						<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
						<link rel="stylesheet" href="./Module/css/index.css">
						<link rel="stylesheet" href="./Module/dhtmlx/skins/skyblue/dhtmlx.css">
						
						<script type="text/javascript" src="./Module/dhtmlx/codebase/dhtmlx.js"></script>
						<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
						
					</head>
		';
		$html .= '
				<body>
				<div style="height:35px !important;  text-align:center; background-color:#e2efff;" >
					<div id="mainMenu"> </div>
				</div>
				<div style="width:100%; background-color:#e2efff; " > 
					<div id="mainToolbar"> </div>
				</div>	

				<script>
					doOnLoad();
				</script>
					
		';

		$html .= '</body></html>';

	// render 
		echo $html;
