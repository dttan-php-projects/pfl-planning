<?php
	date_default_timezone_set("Asia/Bangkok");
	
	$dbMi = mysqli_connect("147.121.59.138","production","PDELS&Auto@{2020}","Avery");
	$dbMi->query("SET NAMES 'utf8'");
	$dbMi2 = mysqli_connect("147.121.73.252","production","PDELS&Auto@{2020}","Avery");
	$dbMi2->query("SET NAMES 'utf8'");

	function Connect138($db=null) {
		if ($db==null) $db = "avery_pfl";
		return mysqli_connect("147.121.59.138","production","PDELS&Auto@{2020}",$db);
	}

	function Connect252($db=null) {
		if ($db==null) $db = "avery_pfl";
		return mysqli_connect("147.121.73.252","production","PDELS&Auto@{2020}",$db);
	}

	function Connect246($db=null) {
		if ($db==null) $db = "avery";
		return mysqli_connect("147.121.59.246","intranet","Avery!123",$db);
	}

	function MiQuery($Query,$conn = null) {
		if($conn == null) {
			$conn = Connect138("avery_pfl");
		}
		$result = $conn->query($Query);		
		if(!$result) {
			echo $conn->error;
			return $conn->error;
		} else {
			return mysqli_fetch_all($result,MYSQLI_ASSOC);
		}
	}		

	function MiQueryScalar($Query,$conn = null)
	{
		if($conn == null) {
			$conn = Connect138("avery_pfl");
		}
		$result = $conn->query($Query);	
		if(!$result) {
			echo $conn->error;
			return $conn->error;
		} else {
			$row = mysqli_fetch_assoc($result);
			if($row != null) {
				foreach($row as $K => $V){
					return $V;
				}
			} else {
				return null;
			}
		}
	}

	function MiNonQuery($Query,$conn = null)
	{
		if($conn == null) {
			$conn = Connect138("avery_pfl");
		}
		if(!$conn->query($Query)){
			echo $conn->error  . "-" . $Query;
			return $conn->error;
		} else {
			return "OK";
		}
	}

	

	function ExecNonQuery($Query,$conn = null) {
		if($conn == null) {
			$conn = Connect138("avery_pfl");
		}

		if(!$conn->query($Query)) {
			echo $conn->error  . "-" . $Query;
			return false;
		} else return true;
	}
	
?>