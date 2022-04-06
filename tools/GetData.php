<?php 
    ini_set('max_execution_time', 3000);
    require("../Module/Database.php");
    
    if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADORDERLIST"){

        $DateFrom = date('Y-m-d',strtotime($_GET["F"]));
        $DateTo = date('Y-m-d',strtotime($_GET["T"]));
        
        
        header('Content-type: text/xml');
        echo "<rows>";
        if(isset($_GET["JOBJACKET"]) && $_GET["JOBJACKET"] != "") {
            $KEY = $_GET["JOBJACKET"];
            $retval = MiQuery( "SELECT ID,JobJacket,SOLine,ItemCode,ReceivingDate,Dueday,Qty,NumSize,QtyScrap,Line,Teeth,RateScrap,PersonPrint,QtyNeed,QtyExport,PersonPIC,RemarkTop,RemarkBot,PONumber,LengthLabel,WidthLabel,MaterialCode,InkNum,InkCode,PrintMethod,CutMethod,FoldMethod,SizeFinish,Drying,Temp,RemarkJJ,Created
            FROM pfl_order_list WHERE JobJacket = '$KEY' ORDER BY ID DESC;",_conn1());
        } else {
            $retval = MiQuery( "SELECT ID,JobJacket,SOLine,ItemCode,ReceivingDate,Dueday,Qty,NumSize,QtyScrap,Line,Teeth,RateScrap,PersonPrint,QtyNeed,QtyExport,PersonPIC,RemarkTop,RemarkBot,PONumber,LengthLabel,WidthLabel,MaterialCode,InkNum,InkCode,PrintMethod,CutMethod,FoldMethod,SizeFinish,Drying,Temp,RemarkJJ,Created, UEE
            FROM pfl_order_list WHERE Created BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND pfl_order_list.JobJacket <> '' ORDER BY ID DESC;",_conn1());
        }

            $index = 0;
            foreach($retval as $row) {

                $index++;
                echo '<row id="'. $row["ID"] .'">';
                echo '<cell>'.$index.'</cell>';
                echo '<cell>' .$row["ID"]. '</cell>';
                echo '<cell>' .$row["ReceivingDate"]. '</cell>';
                echo '<cell>' .$row["JobJacket"]. '</cell>';
                echo '<cell>' .$row["SOLine"]. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row["ItemCode"]). '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$row["PONumber"]). '</cell>';
                echo '<cell></cell>';
                echo '<cell>' .$row["Qty"]. '</cell>';
                echo '<cell>' .$row["QtyNeed"]. '</cell>';
                echo '<cell>' .$row["NumSize"]. '</cell>';
                echo '<cell>' .$row["LengthLabel"]. '</cell>';
                echo '<cell>' .$row["MaterialCode"]. '</cell>';
                echo '<cell></cell>';
                echo '<cell>' .date("Y-m-d", strtotime($row["Dueday"])) . '</cell>';
                echo '<cell></cell>';
                echo '<cell>' .$row["InkNum"]. '</cell>';
                echo '<cell>' .$row["Teeth"]. '</cell>';
                echo '<cell>' .str_replace("&", "&amp;", $row["RemarkJJ"]). '</cell>';
                echo '<cell>' .str_replace("&", "&amp;", $row["PersonPIC"]). '</cell>';
                echo '<cell>' . $row["UEE"]. '</cell>';
                echo '<cell>' .$row["Created"]. '</cell>';
                echo '<cell style="text-align:center;font-weight:bold;color:red;font-size:14pt"><![CDATA[<a onclick="PrintJJ(\'' . $row["JobJacket"] . '\')">Print</a>]]></cell>';
                echo '</row>';
            }
        echo "</rows>";
        
    }