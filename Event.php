<?php 
	ini_set('max_execution_time', 3000);
    require("./Module/Database.php");
    $OrderHandler = "";
    //VNRISIntranet
    if(!isset($_COOKIE["ZeroIntranet"])) {
        if(!isset($_COOKIE["VNRISIntranet"])) $OrderHandler = "Guest"; 
        else $OrderHandler = $_COOKIE["VNRISIntranet"];    
    } 
    else $OrderHandler = $_COOKIE["ZeroIntranet"];
    $OrderHandler = (strpos($OrderHandler,'@') !==false ) ? explode("@",$OrderHandler)[0] : $OrderHandler ;

    // @tandoan - 20201217: @Thi.LeBich sent to mail: PFL Metrics Audit Q4-2020
    function getUEE($qty, $length, $countSize) 
    {
        $qty = !empty($qty) ? (int)$qty : 1;
        $length = !empty($length) ? (float)$length : 1;
        $countSize = !empty($countSize) ? (int)$countSize : 1;
        $result = 0;
        $A = 3;
        $B = 15;
        $D = 4;
        $G = 0.5;
        $perLength = $length/1000;

        if (is_numeric($qty) && is_numeric($length) && is_numeric($countSize) ) {
            $result = ( ( (($qty/500) * $A) + $B) * $perLength) + ( (($D * $countSize) * $perLength) + ($G * $perLength) * ($qty/300) ) + ($qty * $perLength);
        }

        return $result;
    }

    if(isset($_POST["EVENT"]) && isset($_POST["ITEMCODE"]) && $_POST["EVENT"] == "SAVESIZEITEM") {
        $ItemCode = $_POST["ITEMCODE"];
        $Length = $_POST["LENGTH"];
        $Width = $_POST["WIDTH"];
        $Teeth = $_POST["TEETH"];
        
        $StringQuery = "INSERT INTO `pfl_item_dimension`
                                (
                                `Item_Code`,
                                `Label_Length`,
                                `Label_Width`,
                                `Teeth`,
                                `Updated`)
                                VALUES
                                (
                                '$ItemCode',
                                '$Length',
                                '$Width',
                                '$Teeth',
                                NOW());";

        echo MiNonQuery($StringQuery,_conn1());
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "SAVEITEMCODE") {
        $DataRaw = json_decode($_POST["Data"]);
        $retval = MiNonQuery( "DELETE FROM pfl_item_master WHERE Item_Code = '" . $DataRaw->Item_Code . "';",_conn1());
        $N = array();
        $V = array();
        foreach($DataRaw as $C=>$R) {
            $N []= $C;
            $V []= $R;
        }

        $StringQuery = "INSERT INTO pfl_item_master (" . implode(",",$N) . ") VALUE('" . implode("','",$V) . "')";
        echo MiNonQuery( $StringQuery,_conn1());
    } else if(isset($_GET["EVENT"]) && isset($_GET["JOBJACKET"]) && $_GET["EVENT"] == "DELETEJJ") {
        MiNonQuery("DELETE FROM pfl_order_line WHERE IDOrder = '" . $_GET["JOBJACKET"] . "';",_conn1());
        echo "DELETE FROM pfl_order_line WHERE IDOrder = '" . $_GET["JOBJACKET"] . "';";
        echo MiNonQuery("DELETE FROM pfl_order_list WHERE JobJacket = '" . $_GET["JOBJACKET"] . "';",_conn1());
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "CREATEDJJ") {
        
        // $Data = '{"EVENT":"CREATEDJJ",
        //     "MAIN":"{\"RemarkJJ\":\"W\",\"Teeth\":\"64\",\"JobJacket\":\"CP2112-54053\",\"ORDER_TYPE\":0,\"RBO\":\"EUROPE ADIDAS\",\"CustomerItem\":\"62754743\",\"SOLine\":\"64113020-1\",\"ReceivingDate\":\"2021-12-30\",\"RequestDate\":\"01-01-2022\",\"Dueday\":\"01-01-2022\",\"PromiseDate\":\"01-01-1970\",\"LengthLabel\":\"160 \",\"MaterialCode\":\"THSE004751\",\"InkNum\":\"1\",\"InkCode\":\"BLACK(LOGOS and WORDING)\",\"ItemCode\":\"CB408952\",\"Qty\":\"39896\",\"NumSize\":\"4\",\"QtyScrap\":\"181\",\"RateScrap\":\"3%\",\"WidthLabel\":\"35 \",\"QtyNeed\":\"7162\",\"ItemDescription\":\"\",\"Line\":\"\",\"PrintMethod\":\"Two Side\",\"CutMethod\":\"SONIC CUT\",\"FoldMethod\":\"BOOKLET FOLD\",\"Drying\":\"\",\"Temp\":\"\",\"RemarkTop\":\"Them 7% keo\\n- Tham khao chat luong in mau approved cua Project Cost Down (62719090)\",\"RemarkBot\":\"Lay mau: 15pcs/size bat ky\\nDa bu hao vat tu tren don hang\"}","FOD":"","REMARK":"","SO":"undefined|64113020-1",
        //     "SIZE":[{"production_line":"pfl","no_number":"CP2112-54053","so_line":"64113020-1","size":"36","qty":"7979"},{"production_line":"pfl","no_number":"CP2112-54053","so_line":"64113020-1","size":"40","qty":"10772"},{"production_line":"pfl","no_number":"CP2112-54053","so_line":"64113020-1","size":"44","qty":"11171"},{"production_line":"pfl","no_number":"CP2112-54053","so_line":"64113020-1","size":"48","qty":"9974"}]}';
        
    
        
        $DataRaw = json_decode($_POST["MAIN"]);
        $FOD = json_decode($_POST["FOD"]);
        $REMARK = json_decode($_POST["REMARK"]);
        $JobJacket = $DataRaw->JobJacket;

        if($DataRaw->JobJacket != "") {
            $SqlValue = "";
            foreach($DataRaw as $C=>$R) $SqlValue .= ", $C = '" . str_replace("'","\\'",str_replace("\\","\\\\", trim($R))) . "'";
            MiNonQuery("UPDATE pfl_order_list SET FOD = '$FOD', PersonPIC = '$OrderHandler' $SqlValue WHERE JOBJACKET = '$JobJacket'",_conn1());
            MiNonQuery("DELETE FROM pfl_order_line WHERE IDOrder = '$JobJacket'",_conn1());
            
            MiNonQuery("DELETE FROM pfl_size_save WHERE no_number = '$JobJacket'",_conn1());

            echo $DataRaw->JobJacket;
        } else {
            $N = array();
            $V = array();
            $Teeth = $DataRaw->Teeth;
            $SOLine = $DataRaw->SOLine;
            $FR = $DataRaw->ORDER_TYPE;

            // @tandoan - 20201217: update UEE
                $Qty = $DataRaw->Qty;
                $LengthLabel = $DataRaw->LengthLabel;
                $NumSize = $DataRaw->NumSize;
                $UEE = getUEE($Qty, $LengthLabel, $NumSize);
                $UEE = $UEE ? round($UEE) : 0;

            foreach($DataRaw as $C=>$R) {
                $N []= $C;
                $V []= str_replace("'","\\'",$R);
            }
            
            $StringQuery = "INSERT INTO pfl_order_list (" . implode(",",$N) . ") VALUE('" . implode("','",$V) . "')";
            MiNonQuery( $StringQuery,_conn1());
            if(strpos(strtoupper($Teeth),"SILK") !== false) $CPCode = "SP" . Date("ym");
            else $CPCode = "CP" . Date("ym");
            MiNonQuery("UPDATE pfl_order_list SET JOBJACKET = CONCAT('$CPCode-',RIGHT(CONCAT('0000',ID),5)), PersonPIC = '$OrderHandler', UEE='$UEE' WHERE SOLINE = '$SOLine' AND JOBJACKET = ''",_conn1());

            $JobJacket = MiQueryScalar("SELECT JOBJACKET FROM pfl_order_list WHERE SOLINE = '$SOLine' ORDER BY ID DESC LIMIT 1",_conn1());
            
            echo $JobJacket;
            
        }


        $SO = explode("|",$_POST["SO"]);
        $SqlInsert = array();
        foreach($SO as $R) {
            $Data = explode("-",$R);
            if(count($Data) > 1) {
                $SqlInsert []= "('$R','" . $Data[0] . "','$JobJacket','" . $Data[1] . "','$REMARK')";
            }
        }
        
        MiNonQuery("INSERT INTO  pfl_order_line (SOLine,SO,IDOrder,LineNumber,REMARK) VALUES " . implode(",",$SqlInsert),_conn1());


        $sqlSize = array();
        // $SizeData = json_decode($_POST["SIZE"]);
        $SizeData = $_POST["SIZE"];
        if (!empty($SizeData ) ) {
            $order = 0;
            foreach ($SizeData as $sizeItem ) {

                $order++;
                $production_line = $sizeItem['production_line'];
                $no_number = $JobJacket;
                $so_line = $sizeItem['so_line'];
                $size = $sizeItem['size'];
                $qty = $sizeItem['qty'];
                $up_date = date('Y-m-d H:i:s');
                $sqlSize[] = "('$production_line', '$no_number', '$so_line', '$size', '$order', '$qty', '$OrderHandler', '$up_date')";
    
            }
    
            MiNonQuery("INSERT INTO  `pfl_size_save` (`production_line`,`no_number`,`so_line`,`size`, `order`, `qty`, `up_user`, `up_date`) VALUES " . implode(",",$sqlSize),_conn1());
        }
        

        

        // // @tandoan - 20201217: update UEE
        // $UEE = getUEE($Qty, $LengthLabel, $NumSize);
        // $UEE = round($UEE);
        // MiNonQuery("UPDATE pfl_order_list SET `UEE` = '$UEE' WHERE SOLINE = '$SOLine' ORDER BY ID DESC LIMIT 1;", _conn1() );

    }else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "INSERTMARKMACHINE"){
        $RawData = json_decode($_POST["DATA"]);
        $IDCode = $_POST["IDCODE"];
        $DataInsert = array();
        foreach($RawData as $k=>$R) {
            if(strpos($R[0],"-") === false) continue;
            $DataInsert []= "('" . implode("','", $R) . "','$IDCode')";
        }
        $StringSQL = "INSERT INTO pfl_machine_mark (JOBJACKET,PRINT_MACHINE,CUT_MACHINE,PLAN_YMD,MATERIAL,STT,PLAN_CUT,STT_CUT,IDC) VALUES " . implode(",", $DataInsert);
        $Result = MiNonQuery($StringSQL,_conn1());
        if($Result) echo $IDCode;
    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "INSERTDUEDATE"){
        $RawData = json_decode($_POST["DATA"]);
        $IDCode = $_POST["IDCODE"];
        $DataInsert = array();
        $PLA = array();
        foreach($RawData as $k=>$R) {
            if(!in_array($R[0],$PLA)) $PLA []= trim($R[0]);
            $D = explode("-",$R[2]);
            $ORDER_NUMBER = $D[0];
            $LINE_NUMBER = "";
            if(count($D) == 2) $LINE_NUMBER = $D[1];
            $DataInsert []= "('" . implode("','", $R) . "','$IDCode','$ORDER_NUMBER','$LINE_NUMBER')";
        }
        $StringSQL = "UPDATE pfl_duedate SET ACTIVE = '0' WHERE SOLINE IN ('" . implode("','", $PLA) . "')";
        $Result = MiNonQuery($StringSQL, _conn1());
        if(!$Result) echo $StringSQL;

        $StringSQL = "INSERT INTO pfl_duedate(SOLINE,Duedate,Remark,IDC,ORDER_NUMBER,LINE_NUMBER) VALUES " . implode(",", $DataInsert);
        $Result = MiNonQuery($StringSQL, _conn1());
        if($Result) echo $IDCode;
        else echo $StringSQL;

    } else if(isset($_POST["EVENT"]) && $_POST["EVENT"] == "DELETELENGTH") { // @tandoan: Xóa Size
        
        $ItemCode = $_POST["ITEMCODE"];
        $DeleteLengthList = json_decode($_POST["DELETELENGTHLIST"]);
        foreach ($DeleteLengthList as $value ) {
            $sql = "DELETE FROM pfl_item_dimension WHERE ID = $value AND Item_Code = '$ItemCode'";
            $result = MiNonQuery($sql, _conn1());
            if (!$result ) { echo $sql; exit(); } 
        }

        echo $result;
    }

    

    
?>