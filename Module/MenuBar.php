<?php 
	require("Database.php");
	
	$ArrMain = array();
	$ArrMain[]= array("id" => "Logo", "text" => "<img style='width:60px;' src='./Module/Images/menu.png'/>", "code"=> "", "items" => array());

	$DataRow = MiQuery("SELECT CodePage, ParentSEQ, SEQ, TITLE FROM au_avery.intranet_menu_2 ORDER BY SEQ;");

	// print_r($DataRow); exit();
	foreach($DataRow as $K=>$R) {
		$I = SearchRecord($ArrMain,$R["ParentSEQ"]);
		if(count($I) == 0) {
			$ArrMain []= array("id" => "separator" . count($ArrMain), "type" => "separator", "code"=> "", "items" => array());
			$ArrMain []= array("id" => $R["CodePage"], "text" => $R["TITLE"], "code"=> $R["SEQ"], "items" => array());
		} else {
			$V = &$ArrMain;
			foreach($I as $r) {
				$V = &$V[$r];
			}
			$V[]= array("id" => $R["CodePage"], "text" => $R["TITLE"], "img" => "App.png", "code"=> $R["SEQ"], "items" => array());
		}
	}

	function SearchRecord($ArrMain, $Parent, $k = 0, $List = array()) {
		foreach($ArrMain as $K=>$R) {
			if($R["code"] == $Parent) {
				$List[]= $K;
				$List[]= "items";
				return $List;
			} else {
				if(count($R["items"]) == 0) continue;
				$Data = SearchRecord($R["items"],$Parent,$K,$List);
				if(count($Data) > 0) {
					$List[]= $K;
					$List[]= "items";
					foreach($Data as $r) {
						$List[]=$r;
					}
				}
				
			}
		}
		return $List;
	}

	echo json_encode($ArrMain);
	// print_r ($ArrMain);

?>