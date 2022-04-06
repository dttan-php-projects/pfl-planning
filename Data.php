<?php
    ini_set('max_execution_time', 3000);
    require("./Module/Database.php");
    // @TanDoan - 20201224: Xử lý - 1 ngày.
    // @TanDoan - 20210923: Xử lý - 2 ngày.
    function subDate($date, $number)
    {
        $date = ( !empty($date) && ($date != '1970-01-01') ) ? $date : '1970-01-01';
        if ($date == '1970-01-01' ) {
            return "1970-01-01";
        }

        $date = date("Y-m-d", strtotime($date . "$number days"));
        if(date("l", strtotime($date)) == "Sunday") $date = date("Y-m-d", strtotime($date . "-1 days"));
        
        return $date;

    }

    function checkOrder24h48h($ORDER_TYPE)
    {
        $result = false;
        if (!empty($ORDER_TYPE)) {
            $ORDER_TYPE = strtoupper($ORDER_TYPE);
            if (strpos($ORDER_TYPE, 'VN QR 24H') !== false || strpos($ORDER_TYPE, 'VN QR 48H') !== false ) {
                $result = true;
            }
        }

        return $result;
    }

    function getAutomailData($SOLine )
    {

        $res = array();
        $table = "vnso";
        $sql = '';

        if(strpos($SOLine, "-") == 8) {
            $ORDER_NUMBER = explode("-",$SOLine)[0];
            $LINE_NUMBER = explode("-",$SOLine)[1];

            if(!empty($ORDER_NUMBER) && !empty($LINE_NUMBER) ) {

                $sql = "SELECT `VIRABLE_BREAKDOWN_INSTRUCTIONS` as `ATTACHMENT`, `QTY` FROM $table WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY `ID` DESC LIMIT 1;";
                $res = MiQuery($sql, _conn1('au_avery') );

                if (empty($res) ) {
                    $table = "vnso_total";
                    $sql = "SELECT `VIRABLE_BREAKDOWN_INSTRUCTIONS` as `ATTACHMENT`, `QTY` FROM $table WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY `ID` DESC LIMIT 1;";
                    $res = MiQuery($sql, _conn1('au_avery') );
                }

                // get data
                if (!empty($res) ) {
                    $res = $res[0];
                }

            }

        }


        return $res;

    }


    // @TanDoan - 20211230: Lấy size mới nhất
    function getSize($SOLine )
    {
        //init var
        $dataResults = array();
        $size = '';
        $qty = 0;
        $errorCount = 0;
        $check_exist = 0;
        $pause = 0;
        $sizepos = -1;
        $sizepos2 = -1;
        $qtypos = -1;
        $maxpos = 0;
        $qtyTotal = 0;

        // các giá trị tiêu đề có thể có
        $sizeArr = array('SIZE');
        $sizeArr2 = array('STYLE', 'COLOR', 'DPCI' );
        $qtyArr = array("QUANTITY", "QTY", "Q'TY" );

        $automail = getAutomailData($SOLine );
        
        if (empty($automail) ) {
            return $dataResults;
        }

        
        // attachment, qty
        $string = trim($automail['ATTACHMENT']);
        $qtyOrder = trim($automail['QTY']);



        if (empty($string ) || ( (strpos(strtoupper($string),'KEM PACKING LIST CHI TIET') !== false) && strlen($string) <30) ) {
            array_push($dataResults,[ 'size' => 'NON', 'qty' => $qtyOrder ]);
            $qtyTotal = $qtyOrder;
        } else {
            //loại bỏ các đoạn thừa
            $string = str_replace("  ", " ",$string);
            $string = trim($string);

            // Trường hợp TOTAL hoặc TTL (viết tắt)
            if ((strpos(strtoupper($string), ";TOTAL")!==false) || (strpos(strtoupper($string), ";TTL")!==false) ) {

            } else if (strpos(strtoupper($string),"TOTAL")!==false ) {
                $string = str_replace("Total", ";Total",$string);
                $string = str_replace("TOTAL", ";Total",$string);
                $string = str_replace("total", ";Total",$string);
            }  else if (strpos(strtoupper($string),"TTL")!==false ) {
                $string = str_replace("Ttl", ";Total",$string);
                $string = str_replace("TTL", ";Total",$string);
                $string = str_replace("ttl", ";Total",$string);
            }

            if (strpos($string,"\t")!==false ) {
                $string = str_replace("", "\t",$string);
            }
            if (strpos($string,"\x0B")!==false ) {
                $string = str_replace("", "\x0B",$string);
            }

            //Lấy Ký tự cuối check xem phải là ký tự: ^ hay k, k phải thì trả về lỗi
            $check = substr( $string,  strlen($string)-1, 1 );
            if ($check !== '^') {$pause = 1;}

            //Tách chuỗi thành mảng, mỗi phần tử có các nội dung size, color, qty, material_code
            $string_explode = explode(";",$string);

            // Xác định vị trí lớn nhất (mục đích xác định dữ liệu bị loại bỏ hay không)
            foreach ($string_explode as $stringpos) {
                $detachedpos = explode(":",$stringpos);
                if(count($detachedpos) > $maxpos ) $maxpos = count($detachedpos);
            }

            

            //Đoạn code xác định vị trí size, color, qty, material_code.
            foreach ($string_explode as $keyStr => $stringpos) {

                $detachedpos = explode(":",$stringpos);

                for ($i=0;$i<count($detachedpos);$i++) {

                    // Lấy vị trí của SIZE
                    if ($sizepos == -1 ) {

                        foreach ($sizeArr as $sizeTitle ) {
                            if (strpos(strtoupper($detachedpos[$i]),$sizeTitle ) !==false ) {
                                $sizepos=$i;
                                break;
                            }
                        }

                    }

                    // Trường hợp đơn ADIDAS/BOWKER thì ưu tiên lấy Sourcing Size
                    if ( (strpos(strtoupper($detachedpos[$i]),"SOURCINGSIZE")!==false) || strpos(strtoupper($detachedpos[$i]),"SOURCING SIZE")!==false ) {
                        $sizepos=$i;
                    }

                    // Đoạn xác vị trí SIZE là STYLE
                    if ($sizepos == -1 ) {
                        foreach ($sizeArr2 as $sizeTitle2 ) {
                            if (strpos(strtoupper($detachedpos[$i]),$sizeTitle2 ) !==false ) {
                                $sizepos2=$i;
                                break;
                            }
                        }
                    }

                    // Lấy vị trí của QTY
                    if ($qtypos == -1 ) {
                        foreach ($qtyArr as $qtyTitle ) {

                            if (strpos(strtoupper($detachedpos[$i]),$qtyTitle ) !==false ) {
                                $qtypos=$i;
                                break;
                            }
                        }
                    }


                }

                // Trường hợp đã xác định được vị trí size và qty thì dừng
                if ($sizepos >= 0 && $qtypos >= 0) break;

            }

            if($sizepos == -1 && $sizepos2 >= 0 ) {
                $sizepos = $sizepos2;
            }

            // Loại bỏ các dữ liệu không cần thiết
            $character_check = [ 'TOTAL', 'TTL', 'SIZE', 'PACKING LIST', '^' ];
            foreach ($string_explode as $keyStr => $stringpos) {
                $detachedpos = explode(":",$stringpos);
                // Không đúng kích thước loại bỏ
                if(count($detachedpos) < $maxpos ) {
                    unset($string_explode[$keyStr]);
                    continue;
                }

                $checkC = 0;
                foreach ($character_check as $check ) {
                    if (strpos(strtoupper($stringpos), $check) !== false ) {
                        unset($string_explode[$keyStr]);
                        $checkC = 1;
                        break;
                    }
                }

                // Trường hợp có các nội dung cần check thì thoát khỏi vòng lặp
                if ($checkC == 1 ) break;

            }

            
            /* *** Check trường hợp OE không nhập dấu ; trước chữ Total, dấu ^, (còn thì thêm vào ...) *** */
            $character_error_arr = [ 'TOTAL', '^' ];
            $character_error_arr2 = [ 'TOTAL', 'STYLE', 'SIZE', '^' ];

            
            //Nếu có data và có ký tự ^ (data k bị mất). Trường hợp ngược lại không them vào
            if(!empty($string_explode) && !$pause){

                foreach ($string_explode as $key => $value) {
                    $check_exist = 0;
                    //get format string  detached.
                    $detachedStringAll = trim($value);

                    

                    //check error. Nếu không đúng định dạng => return error
                    if(substr_count($detachedStringAll,":")<1){//Trường hợp min = 2 col. tức là không có đủ SIZE & QTY
                        $errorCount++; continue;
                    }

                    
                    //tách chuỗi thành mảng bởi ký tự :
                    $detachedString = explode(":",$detachedStringAll);

                    //check detachedString không đúng định dạng. Dừng
                    if (count($detachedString) !=$maxpos) {$errorCount++; continue;}
                    
                    //get data
                    if ( $sizepos!=$qtypos ) {

                        //lấy dữ liệu //Trường hợp không lấy được cột data nào thì cho dữ liệu đó = rỗng.
                        $size = isset($detachedString[$sizepos]) ? (string)trim($detachedString[$sizepos]) : '';
                        $qty = isset($detachedString[$qtypos]) ? $detachedString[$qtypos] : '';
                        $qty = str_replace(",", "",$qty);
                        $qty = str_replace(".", "",$qty);
                        $qty = !empty($qty) ? (int)str_replace(" ", "",$qty) : '';

                        

                        if (!(int)$qty ) continue;

                        //Tìm các dữ liệu thừa để tách chuỗi thành mảng từ ký tự đó và lấy ra phần tử dữ liệu đã tách.
                        foreach ($character_error_arr as $key => $value) {
                            if (strpos(strtoupper($size),strtoupper($value))!==false) {
                                $detached_tmp = explode($value,$size);
                                $size = $detached_tmp[0];
                            }

                            if (strpos(strtoupper($qty),strtoupper($value))!==false) {
                                $detached_tmp = explode($value,$qty);
                                $qty = $detached_tmp[0];
                            }

                        } //end for

                    }

                    
                    if(!is_numeric($qty) ){
                        //kiểm tra qty có phải số không. Chặn các trường hợp không phải SIZE, QTY cần lấy
                        $errorCount++;
                        
                        continue;

                    } else {

                        // Check trường hợp Size là các ký tự total, ^
                        foreach ($character_error_arr2 as $check ) {
                            if (strpos(strtoupper($size),strtoupper($check))!==false) {
                                $size = '';
                                break;
                            }
                        }

                        // Nhung yêu cầu: tách tất cả size, không cộng dồn

                        //Không tồn tại thì thêm vào mảng kết quả
                        if( ($check_exist == 0) && ($size != '') ) {
                            
                            $get = [ 'size' => $size, 'qty' => $qty ];
                            array_push($dataResults,$get);
                            // total
                            $qtyTotal += $qty;

                        }

                        // Bỏ đoạn code này
                            // //check data ton tai chua, neu ton tai => cong them vao qty. Không tồn tại thì bỏ vào mảng
                            // if (!empty($dataResults)) {

                            //     foreach($dataResults as $key => $value){

                            //         if( $value['size']==$size ){
                            //             $dataResults[$key]['qty'] += $qty;//cộng thêm vào
                            //             $check_exist = 1;

                            //             // total
                            //             $qtyTotal += $qty;
                            //         }
                            //     }

                            //     //Không tồn tại thì thêm vào mảng kết quả
                            //     if( ($check_exist == 0) && (!empty($size) ) ){
                            //         $get = [ 'size' => $size, 'qty' => $qty ];
                            //         array_push($dataResults,$get);
                            //         // total
                            //         $qtyTotal += $qty;

                            //     }

                            // } else {//trường hợp đầu tiên

                            //     //Không tồn tại thì thêm vào mảng kết quả
                            //     if( ($check_exist == 0) && (!empty($size) ) ){

                            //         $get = [ 'size' => $size, 'qty' => $qty ];
                            //         array_push($dataResults,$get);
                            //         // total
                            //         $qtyTotal += $qty;

                            //     }
                            // }
                        //

                    }

                }



            }
        }


        // Check qtyTotal với QTY của đơn hàng. Nếu khác ==> Format SIZE có vấn đề
        if ($qtyTotal != $qtyOrder ) {
            
            $dataResults = array();
        }

        // //return result data
        return $dataResults;

    }


    // $SO = $_GET['SO'];
    // echo "SIZE $SO: "; print_r(getSize($SO));exit();

    if(isset($_GET["EVENT"]) && isset($_GET["SO"]) && $_GET["EVENT"] == "LOADSO") {
        $ArrayJJ = array();
        $SO = $_GET["SO"];
        $SO = str_replace(" ","",$SO);
        $SO = str_replace("\t","",$SO);
        if($SO == "") return;
        $SOLine = "";
        if(strlen($SO) > 8){
            $SOLine = $SO;
            $SO = substr($SO,0,8);
        }

        $ArrayLine = array();
        $DataRow = MiQuery( "SELECT DISTINCT A.ORDER_NUMBER, A.LINE_NUMBER, A.ITEM AS Internal_Item, A.ORDERED_ITEM AS CUSTOMER_ITEM, '' AS TOTALSIZE,
        A.QTY, A.REQUEST_DATE, A.PROMISE_DATE, A.SOLD_TO_CUSTOMER, A.ORDER_TYPE_NAME AS ORDER_TYPE,
        '' AS JOBJACKET FROM au_avery.vnso_total A WHERE A.ORDER_NUMBER = '$SO' AND (A.ITEM LIKE 'P%' OR A.ITEM LIKE 'N%' OR A.ITEM LIKE 'CB%' OR A.ITEM LIKE 'B%') ORDER BY CAST(A.LINE_NUMBER AS SIGNED) ASC;",_conn1());

        foreach($DataRow as $R) $ArrayLine []= $R["LINE_NUMBER"];
        $DataJobJacket = MiQuery( "SELECT SO AS ORDER_NUMBER, LINENUMBER AS LINE_NUMBER, IDOrder AS JOBJACKET, (SELECT REMARKJJ FROM pfl_order_list WHERE JOBJACKET = IDOrder LIMIT 1) AS R FROM pfl_order_line WHERE SO = '$SO' AND LineNumber IN ('" . implode("','", $ArrayLine). "') AND (REMARK IS NULL OR REMARK = '')",_conn1());
        // $DataSize = MiQuery( "SELECT ORDER_NUMBER, LINE_NUMBER, COUNT(ORDER_NUMBER) AS TOTAL FROM au_avery.vnso_size WHERE ORDER_NUMBER = '$SO' AND LINE_NUMBER IN ('" . implode("','", $ArrayLine). "') GROUP BY ORDER_NUMBER, LINE_NUMBER",_conn1());

        header('Content-type: text/xml');
        echo "<rows>";

            foreach($DataRow as $R) {
                foreach($R as $X=>$C) $R[$X] = str_replace("&","&amp;",$C);
                $JobJacket = "";
                $TotalSize = "";
                $Rx = "";

                // @TanDoan - 20201224: xử lý -1 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày
                // Trường hợp ORDER_TYPE_NAME là VN QR 24H, VN QR 48H khong tru
                // @TanDoan - 20210923: xử lý -2 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày // Trường hợp ORDER_TYPE_NAME là VN QR 24H, VN QR 48H khong tru
                $RequestDate = date('Y-m-d',strtotime($R["REQUEST_DATE"]));
                $PromiseDate = ( ($R["PROMISE_DATE"] != "") && ($R["PROMISE_DATE"] != " ") && ($R["PROMISE_DATE"] != "1970-01-01") ) ?  date('Y-m-d',strtotime($R["PROMISE_DATE"])) : "1970-01-01";
                if (!checkOrder24h48h($R["ORDER_TYPE"])  ) {
                    $RequestDate = subDate($RequestDate, -2);
                    $PromiseDate =  subDate($PromiseDate, -2);
                }


                foreach($DataJobJacket as $r) {
                    if($R["ORDER_NUMBER"] == $r["ORDER_NUMBER"] && $R["LINE_NUMBER"] == $r["LINE_NUMBER"]) {
                        if($r["JOBJACKET"] != "") {
                            $JobJacket = $r["JOBJACKET"];
                            $Rx = $r["R"];
                            break;
                        }
                    }
                }

                $SOLine = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];

                // Lấy size
                $sizeData = getSize($SOLine);
                $TotalSize = !empty($sizeData ) ? count($sizeData) : 1;

                echo '<row id="'. $SOLine .'">';
                    echo '<cell>' .$SOLine. '</cell>';
                    echo '<cell>' .$R["Internal_Item"]. '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$R["CUSTOMER_ITEM"]). '</cell>';
                    echo '<cell>' .$R["QTY"]. '</cell>';
                    echo '<cell>' .date("d-m-Y", strtotime($RequestDate)). '</cell>';
                    echo '<cell style="background: pink; font-weight: bold">' . $TotalSize . '</cell>';
                    echo '<cell>' .str_replace("&"," ",$R["SOLD_TO_CUSTOMER"]). '</cell>';
                    echo '<cell>'. $RequestDate .'</cell>';
                    echo '<cell>'. date("d-m-Y", strtotime($PromiseDate)) .'</cell>';

                    echo '<cell ' .($JobJacket != "" ? "style='background:red;color:white'" : ""). '>'.$JobJacket .'</cell>';
                    echo '<cell>' . str_replace("&","&amp;",$Rx) . '</cell>';
                echo '</row>';

                // $T = 1;

            }

        echo "</rows>";
    } else if(isset($_GET["EVENT"]) && isset($_GET["JOBJACKET"]) && $_GET["EVENT"] == "LOADSOFROMJOB") {
        $ArrayJJ = array();
        $JOBJACKET = $_GET["JOBJACKET"];
        if($JOBJACKET == "") return;

        $ArraySO = array();
        $DataJobJacket = MiQuery( "SELECT SO AS ORDER_NUMBER, LINENUMBER AS LINE_NUMBER, IDOrder AS JOBJACKET FROM pfl_order_line WHERE IDOrder = '$JOBJACKET'",_conn1());
        foreach($DataJobJacket as $R) {
            if(!in_array($R["ORDER_NUMBER"],$ArraySO)) $ArraySO []= $R["ORDER_NUMBER"];
        }

        $DataRow = MiQuery( "SELECT DISTINCT A.ORDER_NUMBER, A.LINE_NUMBER, A.ITEM AS Internal_Item, A.ORDERED_ITEM AS CUSTOMER_ITEM, '' AS TOTALSIZE,
        A.QTY, A.REQUEST_DATE, A.PROMISE_DATE, A.SOLD_TO_CUSTOMER, ORDER_TYPE_NAME AS ORDER_TYPE,
        '' AS JOBJACKET FROM au_avery.vnso_total A WHERE A.ORDER_NUMBER IN ('" . implode("','",$ArraySO) . "') AND (A.ITEM LIKE 'P%' OR A.ITEM LIKE 'N%' OR A.ITEM LIKE 'CB%' OR A.ITEM LIKE 'B%') ORDER BY CAST(A.LINE_NUMBER AS SIGNED) ASC;",_conn1());

        header('Content-type: text/xml');
        echo "<rows>";

            foreach($DataRow as $R) {
                $JobJacket = "";
                $TotalSize = "";

                foreach($DataJobJacket as $r) {
                    if($R["ORDER_NUMBER"] == $r["ORDER_NUMBER"] && $R["LINE_NUMBER"] == $r["LINE_NUMBER"]) {
                        if($r["JOBJACKET"] != "") {
                            $JobJacket = $r["JOBJACKET"];
                            break;
                        }
                    }
                }

                if($JobJacket == "") continue;

                // @TanDoan - 20201224: xử lý -1 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày
                // @TanDoan - 20201224: xử lý -1 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày
                // Trường hợp ORDER_TYPE_NAME là VN QR 24H, VN QR 48H
                // @TanDoan - 20210923: xử lý -2 ngày. Nếu trừ xong và rơi vào chủ nhật thì trừ - 1 ngày // Trường hợp ORDER_TYPE_NAME là VN QR 24H, VN QR 48H khong tru
                $RequestDate = date('Y-m-d',strtotime($R["REQUEST_DATE"]));
                // $PromiseDate = date('Y-m-d',strtotime($R["PROMISE_DATE"]));
                $PromiseDate = ( ($R["PROMISE_DATE"] != "") && ($R["PROMISE_DATE"] != " ") && ($R["PROMISE_DATE"] != "1970-01-01") ) ?  date('Y-m-d',strtotime($R["PROMISE_DATE"])) : "1970-01-01";
                if (!checkOrder24h48h($R["ORDER_TYPE"])  ) {
                    $RequestDate = subDate($RequestDate, -2);
                    $PromiseDate = subDate($PromiseDate, -2);
                }


                $SOLine = $R["ORDER_NUMBER"] . "-" . $R["LINE_NUMBER"];

                // Lấy size
                $sizeData = getSize($SOLine);
                $TotalSize = !empty($sizeData ) ? count($sizeData) : 1;

                echo '<row id="'. $SOLine .'">';
                echo '<cell>' .$SOLine. '</cell>';
                echo '<cell>' .$R["Internal_Item"]. '</cell>';
                echo '<cell>' .str_replace("&","&amp;",$R["CUSTOMER_ITEM"]). '</cell>';
                echo '<cell>' .$R["QTY"]. '</cell>';
                echo '<cell>' .date("d-m-Y", strtotime($RequestDate)). '</cell>';
                echo '<cell style="background: pink; font-weight: bold">' . $TotalSize . '</cell>';
                echo '<cell>' .str_replace("&"," ",$R["SOLD_TO_CUSTOMER"]). '</cell>';
                echo '<cell>'. $RequestDate .'</cell>';
                echo '<cell>'. date("d-m-Y", strtotime($PromiseDate)) .'</cell>';


                echo '<cell ' .($JobJacket != "" ? "style='background:red;color:white'" : ""). '>'.$JobJacket .'</cell>';
                echo '<cell>'. $R["ORDER_NUMBER"] .'</cell>';
                echo '<cell>'. $R["LINE_NUMBER"] .'</cell>';

                echo '</row>';
                $T = 1;
            }

        echo "</rows>";

    } else if(isset($_GET["EVENT"]) && isset($_GET["ITEM"]) && $_GET["EVENT"] == "LOADSIZE") {
        $ArrayMain = array();
        $ITEM = $_GET["ITEM"];
        header('Content-type: text/xml');
        echo "<rows>";
        $TurnS = 0;
        $DataRow = MiQuery( "SELECT DISTINCT ID, Item_Code, Label_Length, Label_Width, Label_Width AS Finish_Width, Teeth, Last_Use FROM pfl_item_dimension WHERE Item_Code = '$ITEM' ORDER BY Last_Use DESC;",_conn1());
        foreach($DataRow as $R) {
                if($TurnS == 0) $TurnS = 1;
                echo '<row id="'. $R["ID"] .'" selected="' . $TurnS . '">';
                echo '<cell>' .trim($R["Label_Length"]). ' mm</cell>';
                echo '<cell>' .trim($R["Label_Width"]). ' mm</cell>';
                echo '<cell>' .$R["Teeth"]. '</cell>';
                echo '<cell>' .$R["Last_Use"]. '</cell>';
                echo '<cell>0</cell>';
                echo '</row>';
			}
        echo "</rows>";
    } else if(isset($_GET["EVENT"]) && isset($_GET["ITEM"]) && $_GET["EVENT"] == "LOADITEM") {
        $ArrayMain = array();
        $KEYWORD = $_GET["ITEM"];
        $Exist = 0;
        $TopDate = "";
        $IDLast = "";
        $DataRow = MiQuery( "SELECT Material_Code AS MaterialCode, Dry AS Drying, Heat AS Temp, Print_Type AS PrintMethod, Cut_Type AS CutMethod,
                                    Fold_Type AS FoldMethod, RemarkTop, RemarkBot, NumInk AS InkNum, Ink AS InkCode
                                    FROM pfl_item_master WHERE Item_Code = '$KEYWORD';",_conn1());
        foreach($DataRow as $R) {
            foreach($R as $F=>$r) $R[$F] = str_replace("<br/>","\n",$r);
            if($R["PrintMethod"] == "Back Side") $R["PrintMethod"] = "Front Side";

            if($R["CutMethod"] == "COLD CUT" && $R["FoldMethod"] == "CUT SINGLE") $R["CutMethod"] = "DIE CUT";
            else if($R["CutMethod"] == "HOT CUT" && $R["FoldMethod"] == "CUT SINGLE") $R["CutMethod"] = "ROLLS";
            else if($R["CutMethod"] == "SONIC CUT" && $R["FoldMethod"] == "CUT SINGLE") $R["CutMethod"] = "SINGLE LASER CUT";
            array_push($ArrayMain, $R);
        }
        echo str_replace("\ufeff","",json_encode($ArrayMain));
    } else if(isset($_GET["EVENT"]) && isset($_GET["SO"]) && $_GET["EVENT"] == "LOADSIZESO") {
        $SOLINE = $_GET["SO"];
        $SOLINEH = explode(",",str_replace("'","",$SOLINE));
        $SQLQuery = "";
        $error = false;

        $data[] = array('No.', 'SIZE');

        foreach($SOLINEH as $SOLINE ) $data[0][] = $SOLINE;
        $columnMax = count($data[0]);

        // print_r($data); exit();

        // Giữ vị trí của soline muốn trả QTY vào. Vị trí đầu tiên là 3 (code là số 2)
        $exist = 0;
        $columnID = 2;
        if (count($SOLINEH) == 1 ) {

            $sizeData = getSize($SOLINE );
            if (empty($sizeData) ) {
                $error = true;
            }
            $rowID=0;
            foreach( $sizeData as $value ) {
                
                $rowID++;

                $size = $value['size'];
                $qty = $value['qty'];

                $data[] = array( $rowID, $size, $qty );

            }
        } else {
            foreach($SOLINEH as $keySO => $SOLINE ) {

                $sizeData = getSize($SOLINE );
                // echo "<br>\nSOLINE: $SOLINE -- ";
                // echo "sizeData: "; print_r($sizeData);exit();
                // foreach($sizeData as $key => $value ) {
                //     echo "<br>\nSizeData $key: "; print_r($value);
                // } exit();
    
                if (empty($sizeData) ) {
                    $error = true;
                    break;
                }
    
                foreach( $sizeData as $key => $value ) {
    
                    $exist = 0;
                    $size = $value['size'];
                    $qty = $value['qty'];
    
                    // Kiểm tra tồn tại, nếu có SIZE trong data thì tại dòng dữ liệu đó thêm QTY vào cột SOLINE tương ứng
                    foreach ($data as $keyC => $valueC ) {
                        if ($size == $valueC[1] ) {
                            // echo "data: "; print_r($data);
                            // echo "<br>\nKey: $keyC && SIZE: $size && check size: " . $checkArr[1]; ;
    
                            // Có SIZE trong data arr
                            if (isset($data[$keyC][$columnID] ) ) {
                                $data[$keyC][$columnID] += $qty;
                            } else{
                                $data[$keyC][$columnID] = $qty;
                            }
    
                            $exist = 1;
                            break;
    
                        }
                    }
    
                        // Trường hợp không có thì thêm dòng mới vào
                    if ($exist == 1 ) continue;
    
                    // Chưa có SIZE trong data arr
                    $sizeMax = count($data);
                    $data[$sizeMax] = array( $sizeMax, $size);
                    $data[$sizeMax][$columnID] = $qty;
    
                }
    
    
    
                $columnID++; // tăng vị trí dòng
    
            } 
        }
        

        // print_r($data); exit();
        // foreach($data as $key => $value ) {
        //     echo "Row $key: ";

        //     for ($colID=0; $colID<$columnMax; $colID++ ) {
        //         if (isset($value[$colID])) echo " $colID - $value[$colID] & ";
        //         else echo "$colID - NON & ";
        //     }
        //     echo "<br>\n";
        // }

        // exit();

        header('Content-type: text/xml');
        echo '<rows>';
            if ($error == true ) {

            } else {
                foreach ($data as $key => $value ) {
                    if ($key == 0 ) {

                        echo '<head>';
                            foreach ($value as $k => $detail ) {
                                if ($k == 0 ) {
                                    echo '<column width="30" type="ro" align="center" sort="str">'.$detail.'</column>';
                                } else {
                                    echo '<column width="100" type="ro" align="center" sort="str">'.$detail.'</column>';
                                }
                            }

                            echo '<settings>';
                                echo '<colwidth>px</colwidth>';
                            echo '</settings>';
                        echo '</head>';
                    } else {
                        echo '<row id="'. $key .'">';
                            for ($colID=0; $colID<$columnMax; $colID++ ) {
                                if (isset($value[$colID]) ) {
                                    echo '<cell>' .$value[$colID]. '</cell>';
                                } else {
                                    echo '<cell></cell>';
                                }

                            }
                        echo '</row>';
                    }
                }
            }

        echo '</rows>';

    } else if(isset($_GET["EVENT"]) && isset($_GET["JOBJACKET"]) && $_GET["EVENT"] == "LOADJOBJACKET") {
        $ArrayMain = array();
        $JOBJACKET = $_GET["JOBJACKET"];
        $DataRow = MiQuery( "SELECT * FROM pfl_order_list WHERE JOBJACKET = '$JOBJACKET' LIMIT 1;",_conn1());
        echo json_encode($DataRow);
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADMARKMACHINE"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            echo "<?xml version='1.0' ?><data>";
            header('Content-type: text/xml');
            foreach(explode(",",$_POST["ids"]) as $K) {
                $ids = $K;
                $C0 = $_POST[$ids . "_c0"];
                $C1 = $_POST[$ids . "_c1"];
                $C2 = $_POST[$ids . "_c2"];
                $C3 = $_POST[$ids . "_c3"];
                $C4 = $_POST[$ids . "_c4"];
                $C5 = $_POST[$ids . "_c5"];
                $C6 = $_POST[$ids . "_c6"];
                $C7 = $_POST[$ids . "_c7"];
                $C8 = $_POST[$ids . "_c8"];
                $CNavi = $_POST[$ids . "_!nativeeditor_status"];

                $D = explode("-",$C3);
                $ORDER_NUMBER = $D[0];
                $LINE_NUMBER = "";
                if(count($D) == 2) $LINE_NUMBER = $D[1];

                if($CNavi == "updated")
                {
                    $SQLString = "UPDATE pfl_machine_mark SET
                                        JOBJACKET = '$C1',
                                        PRINT_MACHINE = '$C2',
                                        CUT_MACHINE = '$C3',
                                        PLAN_YMD = '$C4',
                                        MATERIAL = '$C5',
                                        STT = '$C6',
                                        PLAN_CUT = '$C7',
                                        STT_CUT = '$C8',
                                        CREATEDDATE = NOW()
                                        WHERE ID = '$ids';";
                    MiNonQuery( $SQLString,_conn1());
                } else if($CNavi == "deleted") {
                    $SQLString = "DELETE FROM pfl_machine_mark WHERE ID = '$ids'";
                    MiNonQuery( $SQLString,_conn1());
                }
                echo "<action type='$CNavi' sid='$ids' tid='$ids' ></action>";
            }
            echo "</data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";

            $fiels = 'ID, JOBJACKET, PRINT_MACHINE, CUT_MACHINE, PLAN_YMD, MATERIAL, STT, PLAN_CUT, STT_CUT, CREATEDDATE';
            if(isset($_GET["IDCODE"])) {
                $KEY = $_GET["IDCODE"];
                $retval = MiQuery( "SELECT $fiels FROM pfl_machine_mark WHERE IDC = '$KEY' ORDER BY STT ASC;",_conn1());
            } else if(isset($_GET["JOBJACKET"])) {
                $KEY = $_GET["JOBJACKET"];
                $retval = MiQuery( "SELECT $fiels FROM pfl_machine_mark WHERE JOBJACKET = '$KEY' ORDER BY ID ASC;",_conn1());
            } else {
                $DateFrom = $_GET["F"];
                $DateTo = $_GET["T"];
                $retval = MiQuery( "SELECT $fiels FROM pfl_machine_mark WHERE CreatedDate BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' ORDER BY ID ASC LIMIT 4000;",_conn1());
            }

                foreach($retval as $row) {
                    echo '<row id="'. str_replace("&","&amp;",$row['ID']) .'">';
                    echo '<cell>' .str_replace("&","&amp;",$row['ID']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['JOBJACKET']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PRINT_MACHINE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['CUT_MACHINE']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PLAN_YMD']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['MATERIAL']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['STT']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['PLAN_CUT']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['STT_CUT']). '</cell>';
                    echo '<cell>' .str_replace("&","&amp;",$row['CREATEDDATE']). '</cell>';
                    echo '</row>';
                }
            echo "</rows>";
        }
    } else if(isset($_GET["JOBJACKET"]) && isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADJOBPRINT") {
        $SOLINE = $_GET["JOBJACKET"];
        $JobJacket = "";
        $TurnOffPlan = true;
        $TurnOffOrder = true;
        $Order_Number = "";
        $Line_Number = "";

        $retval = MiQuery( "SELECT IDOrder AS JobJacket, SO, LineNumber FROM pfl_order_line WHERE SOLine = '$SOLINE' ORDER BY ID DESC LIMIT 1;",_conn1());
        foreach($retval as $row) {
            $JobJacket = $row["JobJacket"];
            $Order_Number = $row["SO"];
            $Line_Number = $row["LineNumber"];
        }
        if($JobJacket == "")
        {
            echo "NG|$SOLINE chưa được tạo";
            return;
        }
        $retval = MiQueryScalar( "SELECT JOBJACKET FROM pfl_machine_mark WHERE JOBJACKET = '$JobJacket' ORDER BY ID DESC LIMIT 1;",_conn1());
        if($retval == "")  echo "NO|Đơn hàng $JobJacket chưa được sắp xếp Plan";
        else {
            // $retval = MiQueryScalar( "SELECT ITEM FROM au_avery.vnso WHERE ORDER_NUMBER = '$Order_Number' AND LINE_NUMBER = '$Line_Number';");
            // if($retval == "") echo "NK|Đơn hàng $JobJacket đã bị xóa khỏi Áu tù meow|$JobJacket";
            // else echo "OK|$JobJacket";
            echo "OK|$JobJacket";
        }
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADORDERLIST"){
        if(isset($_GET["editing"]) && $_GET["editing"] == true) {
            echo "<?xml version='1.0' ?><data>";
            header('Content-type: text/xml');
            foreach(explode(",",$_POST["ids"]) as $K) {
                $ids = $K;
                $C0 = $_POST[$ids . "_c0"];
                $C1 = $_POST[$ids . "_c1"];
                $C2 = $_POST[$ids . "_c2"];
                $C3 = $_POST[$ids . "_c3"];
                $C4 = $_POST[$ids . "_c4"];
                $C5 = $_POST[$ids . "_c5"];
                $C6 = $_POST[$ids . "_c6"];
                $C7 = $_POST[$ids . "_c7"];
                $CNavi = $_POST[$ids . "_!nativeeditor_status"];

                $D = explode("-",$C3);
                $ORDER_NUMBER = $D[0];
                $LINE_NUMBER = "";
                if(count($D) == 2) $LINE_NUMBER = $D[1];

                // if($CNavi == "updated")
                // {
                //     $SQLString = "UPDATE pfl_machine_mark SET
                //                         JOBJACKET = '$C1',
                //                         PRINT_MACHINE = '$C2',
                //                         CUT_MACHINE = '$C3',
                //                         PLAN_YMD = '$C4',
                //                         MATERIAL = '$C5',
                //                         STT = '$C6',
                //                         CREATEDDATE = NOW()
                //                         WHERE ID = '$ids';";
                //     MiNonQuery( $SQLString);
                // } else if($CNavi == "deleted") {
                //     $SQLString = "DELETE FROM pfl_machine_mark WHERE ID = '$ids'";
                //     MiNonQuery( $SQLString);
                // }
                // echo "<action type='$CNavi' sid='$ids' tid='$ids' ></action>";
            }
            echo "</data>";
        } else {
            header('Content-type: text/xml');
            echo "<rows>";
            if(isset($_GET["JOBJACKET"]) && $_GET["JOBJACKET"] != "") {
                $KEY = $_GET["JOBJACKET"];
                $retval = MiQuery( "SELECT ID,JobJacket,SOLine,ItemCode,ReceivingDate,Dueday,Qty,NumSize,QtyScrap,Line,Teeth,RateScrap,PersonPrint,QtyNeed,QtyExport,PersonPIC,RemarkTop,RemarkBot,PONumber,LengthLabel,WidthLabel,MaterialCode,InkNum,InkCode,PrintMethod,CutMethod,FoldMethod,SizeFinish,Drying,Temp,RemarkJJ,Created
                FROM pfl_order_list WHERE JobJacket = '$KEY' ORDER BY ID DESC;",_conn1());
            } else {
                $DateFrom = $_GET["F"];
                $DateTo = $_GET["T"];
                $retval = MiQuery( "SELECT ID,JobJacket,SOLine,ItemCode,ReceivingDate,Dueday,Qty,NumSize,QtyScrap,Line,Teeth,RateScrap,PersonPrint,QtyNeed,QtyExport,PersonPIC,RemarkTop,RemarkBot,PONumber,LengthLabel,WidthLabel,MaterialCode,InkNum,InkCode,PrintMethod,CutMethod,FoldMethod,SizeFinish,Drying,Temp,RemarkJJ,Created, UEE
                FROM pfl_order_list WHERE Created BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND pfl_order_list.JobJacket <> '' ORDER BY ID DESC;",_conn1());
            }

                foreach($retval as $row) {
                    echo '<row id="'. $row["ID"] .'">';
                    echo '<cell></cell>';
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
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "GETLASTREMARK"){
        $ITEM = $_GET["ITEM"];
        $LastRemark = MiQueryScalar("SELECT RemarkJJ FROM pfl_order_list WHERE ID IN (
            SELECT ID FROM (SELECT MAX(ID) AS ID FROM pfl_order_list WHERE ITEMCODE = '$ITEM') AS X
        )",_conn1());
        echo $LastRemark;
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADDATADUEDATE"){
        header('Content-type: text/xml');
        echo "<rows>";
        if(isset($_GET["IDCODE"]) && $_GET["IDCODE"] != "") {
            $KEY = $_GET["IDCODE"];
            $retval = MiQuery( "SELECT ID,SOLINE,DUEDATE,REMARK FROM pfl_duedate WHERE IDC = '$KEY' ORDER BY ID DESC;",_conn1());
        } else if(isset($_GET["SOLINE"]) && $_GET["SOLINE"] != "") {
            $KEY = $_GET["SOLINE"];
            $retval = MiQuery( "SELECT ID,SOLINE,DUEDATE,REMARK FROM pfl_duedate WHERE SOLINE = '$KEY' ORDER BY ID DESC;",_conn1());
        } else {
            $DateFrom = $_GET["F"];
            $DateTo = $_GET["T"];
            $retval = MiQuery( "SELECT ID,SOLINE,DUEDATE,REMARK
            FROM pfl_duedate WHERE CreatedDate BETWEEN '$DateFrom 00:00:00' AND '$DateTo 23:59:59' AND ACTIVE ORDER BY ID DESC;",_conn1());
        }

        foreach($retval as $row) {
            echo '<row id="'. $row["ID"] .'">';
            echo '<cell>' .$row["SOLINE"]. '</cell>';
            echo '<cell>' .$row["DUEDATE"]. '</cell>';
            echo '<cell>' .str_replace("&", "&amp;", $row["REMARK"]). '</cell>';
            echo '</row>';
        }
        echo "</rows>";
    }
