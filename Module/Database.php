<?php
	date_default_timezone_set("Asia/Bangkok");

	function _conn1($db=null){
		if ($db==null) $db = "au_avery_pfl";
		return mysqli_connect("147.121.56.227","planning","PELS&Auto@{2020}",$db);
	}

	function MiQuery($Query,$dbMi = null) {
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		$result = $dbMi->query($Query);
		if(!$result) {
			$DataRaw = $dbMi->error;
			echo $DataRaw;
		}
		else $DataRaw = mysqli_fetch_all($result,MYSQLI_ASSOC);
		mysqli_close($dbMi);
		return $DataRaw;
	}		

	function MiQueryScalar($Query,$dbMi = null) {
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		$result = $dbMi->query($Query);	
		if(!$result) {
			$DataRaw = $dbMi->error;
		} else {
			$row = mysqli_fetch_assoc($result);
			if($row != null) {
				foreach($row as $K => $V){
					$DataRaw = $V;
					break;
				}
			} else $DataRaw = null;
		}
		mysqli_close($dbMi);
		return $DataRaw;
	}

	function MiNonQuery($Query,$dbMi = null) {
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		if(!$dbMi->query($Query)){
			echo $dbMi->error;
			mysqli_close($dbMi);
			return false;
		} else {
			mysqli_close($dbMi);
			return true;
		}
	}

	function MiQueryEcho($Query,$dbMi = null) {
		echo $Query;
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		$result = $dbMi->query($Query);
		if(!$result) {
			$DataRaw = $dbMi->error;
			echo $DataRaw;
		}
		else $DataRaw = mysqli_fetch_all($result,MYSQLI_ASSOC);
		mysqli_close($dbMi);
		return $DataRaw;
	}		

	function MiQueryScalarEcho($Query,$dbMi = null) {
		echo $Query;
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		$result = $dbMi->query($Query);	
		if(!$result) {
			$DataRaw = $dbMi->error;
		} else {
			$row = mysqli_fetch_assoc($result);
			if($row != null) {
				foreach($row as $K => $V){
					$DataRaw = $V;
					break;
				}
			} else $DataRaw = null;
		}
		mysqli_close($dbMi);
		return $DataRaw;
	}

	function MiNonQueryEcho($Query,$dbMi = null) {
		echo $Query;
		if($dbMi == null) $dbMi = _conn1();
		$dbMi->query("SET NAMES 'utf8'");
		if(!$dbMi->query($Query)){
			echo $dbMi->error;
			mysqli_close($dbMi);
			return false;
		} else {
			mysqli_close($dbMi);
			return true;
		}
	}
?>