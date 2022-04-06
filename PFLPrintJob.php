<?php 
    require("./Module/Database.php");
    ini_set('memory_limit', '-1'); 
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="Automail_' . Date("YmdHis") . '.csv"');
    header('Pragma: no-cache');
    $out = fopen("php://output", 'w');
    $SQLString = "SELECT JobJacket, SOLine, ItemCode, PrintDate FROM pfl_order_list WHERE PrintDate >= NOW() - INTERVAL 3 DAY;";
    $DataRaw = MiQuery($SQLString, _conn1());

    if(count($DataRaw) != 0) {
        $ArrayMain = array();
        $Index = 0;
        foreach($DataRaw[0] as $C=>$R) {
            $ArrayMain[$Index]= $C;
            $Index++;
        }
        fputcsv($out, $ArrayMain,",");
        foreach($DataRaw as $R) {
            $ArrayMain = array();
            $Index = 0;
            foreach($R as $C=>$K) {
                $ArrayMain[$Index] = $K;
                $Index++;
            }
            fputcsv($out, $ArrayMain,",");
        }
    }
    fclose($out);
?>