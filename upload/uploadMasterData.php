<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ("./vendor/autoload.php"); 
    require_once ('../Module/Database.php');

    $Updated_By = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    $Updated = date('Y-m-d H:i:s');

    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PFL_Master_Data_' . $_SERVER['REMOTE_ADDR'] . '_' . $Updated_By . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $targetPath = './Excel/' . $file_name;
            
            // hàm move_uploaded_file k sử dụng được (có thể do bị hạn chế quyền của thư mục tmp)
            if (copy($_FILES['file']['tmp_name'], $targetPath)) {
                // echo "Đã upload file : $targetPath <br />\n";
            } else {
                $message = "Problem in Importing Excel Data";
            }

            // init PhpSpreadsheet Xlsx
                $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            // get sheet 0 (sheet 1)
                $spreadSheet = $Reader->load($targetPath)->getSheet(0);
                $allDataInSheet = $spreadSheet->toArray(null, true, true, true);

            // check col name exist
                $createArray = array( 
                    'Item_Code', 'Material_Code', 'NumInk', 'Ink', 'Print_Type', 'Cut_Type', 'Fold_Type', 'Dry', 'Heat', 'Finish_Length', 
                    'Finish_Width', 'RemarkTop', 'RemarkBot', 'RemarkJobJacket', 'Printing_Speed'
                );
                $makeArray = array( 
                    'Item_Code' => 'Item_Code', 
                    'Material_Code' => 'Material_Code', 
                    'NumInk' => 'NumInk',
                    'Ink' => 'Ink',
                    'Print_Type' => 'Print_Type',
                    'Cut_Type' => 'Cut_Type',
                    'Fold_Type' => 'Fold_Type',
                    'Dry' => 'Dry',
                    'Heat' => 'Heat',
                    'Finish_Length' => 'Finish_Length',

                    'Finish_Width' => 'Finish_Width',
                    'RemarkTop' => 'RemarkTop',
                    'RemarkBot' => 'RemarkBot',
                    'RemarkJobJacket' => 'RemarkJobJacket',
                    'Printing_Speed' => 'Printing_Speed'
                );
                $SheetDataKey = array();
                foreach ($allDataInSheet as $dataInSheet) {
                    foreach ($dataInSheet as $key => $value) {
                        if (in_array(trim($value), $createArray)) {
                            $value = preg_replace('/\s+/', '', $value);
                            $SheetDataKey[trim($value)] = $key;
                        } else { }
                    }
                }
				
            // check data
                $flag = 0;
                $data = array_diff_key($makeArray, $SheetDataKey);
                if (empty($data)) { $flag = 1; }
            
            // limit 
                $limit = 1000;  
                $count_limit = 0;
                $countCheck = 0; // đếm số dòng được save
                $checkPause = 0;
            
            // load data
                if ($flag == 1) {
                    
                    for ($i = 2; $i <= count($allDataInSheet); $i++) {

                        // get col key
                            $Item_Code = $SheetDataKey['Item_Code']; 
                            $Material_Code = $SheetDataKey['Material_Code']; 
                            $NumInk = $SheetDataKey['NumInk']; 
                            $Ink = $SheetDataKey['Ink']; 
                            $Print_Type = $SheetDataKey['Print_Type']; 
                            $Cut_Type = $SheetDataKey['Cut_Type']; 
                            $Fold_Type = $SheetDataKey['Fold_Type']; 
                            $Dry = $SheetDataKey['Dry']; 
                            $Heat = $SheetDataKey['Heat']; 
                            $Finish_Length = $SheetDataKey['Finish_Length']; 

                            $Finish_Width = $SheetDataKey['Finish_Width']; 
                            $RemarkTop = $SheetDataKey['RemarkTop']; 
                            $RemarkBot = $SheetDataKey['RemarkBot']; 
                            $RemarkJobJacket = $SheetDataKey['RemarkJobJacket']; 
                            $Printing_Speed = $SheetDataKey['Printing_Speed']; 
                        
                        // get data 
                            $Item_Code = filter_var(trim(strtoupper($allDataInSheet[$i][$Item_Code]) ), FILTER_SANITIZE_STRING);
                            $Material_Code = filter_var(trim($allDataInSheet[$i][$Material_Code]), FILTER_SANITIZE_STRING);
                            $NumInk = filter_var(trim($allDataInSheet[$i][$NumInk]), FILTER_SANITIZE_STRING);
                            $Ink = filter_var(trim($allDataInSheet[$i][$Ink]), FILTER_SANITIZE_STRING);
                            $Print_Type = filter_var(trim($allDataInSheet[$i][$Print_Type]), FILTER_SANITIZE_STRING);
                            $Cut_Type = filter_var(trim($allDataInSheet[$i][$Cut_Type]), FILTER_SANITIZE_STRING);
                            $Fold_Type = filter_var(trim($allDataInSheet[$i][$Fold_Type]), FILTER_SANITIZE_STRING);
                            $Dry = filter_var(trim($allDataInSheet[$i][$Dry]), FILTER_SANITIZE_STRING);
                            $Heat = filter_var(trim($allDataInSheet[$i][$Heat]), FILTER_SANITIZE_STRING);
                            $Finish_Length = filter_var(trim($allDataInSheet[$i][$Finish_Length]), FILTER_SANITIZE_STRING);

                            $Finish_Width = filter_var(trim($allDataInSheet[$i][$Finish_Width]), FILTER_SANITIZE_STRING);
                            $RemarkTop = filter_var(trim($allDataInSheet[$i][$RemarkTop]), FILTER_SANITIZE_STRING);
                            $RemarkBot = filter_var(trim($allDataInSheet[$i][$RemarkBot]), FILTER_SANITIZE_STRING);
                            $RemarkJobJacket = filter_var(trim($allDataInSheet[$i][$RemarkJobJacket]), FILTER_SANITIZE_STRING);
                            $Printing_Speed = filter_var(trim($allDataInSheet[$i][$Printing_Speed]), FILTER_SANITIZE_STRING);

                        
                        // check empty data
                            if (empty($Item_Code)) {
                                $checkPause++;
                                if ($checkPause == 2 ) break;
                                continue;
                            } 
                        
                        // get data
                            $updateData[] = array( 
                                'Item_Code' => $Item_Code,
                                'Material_Code' => $Material_Code,
                                'NumInk' => $NumInk,
                                'Ink' => $Ink,
                                'Print_Type' => $Print_Type,
                                'Cut_Type' => $Cut_Type,
                                'Fold_Type' => $Fold_Type,
                                'Dry' => $Dry,
                                'Heat' => $Heat,
                                'Finish_Length' => $Finish_Length,

                                'Finish_Width' => $Finish_Width,
                                'RemarkTop' => $RemarkTop,
                                'RemarkBot' => $RemarkBot,
                                'RemarkJobJacket' => $RemarkJobJacket,
                                'Printing_Speed' => $Printing_Speed,
                                'Updated_By' => $Updated_By,
                                'Updated' => $Updated,
                                
                            );

                        // Nếu đến limit thì save
                            $count_limit = count($updateData); 
                            if ($count_limit == $limit ) {
                                $message = save($updateData, $countCheck);
                                $updateData = array(); // reset data update
                            }
                    }

                    // trường hợp còn lại
                        if (!empty($updateData) ) {
                            $message = save($updateData, $countCheck);
                        }
                }

                
            
        } else {
            $message = "Invalid File Type. Upload Excel File.";
        }
    }


    function save($updateData, $countCheck ) 
    {
        $conn = _conn1();
        $table = "pfl_item_master";
        $table_fod = "pfl_item_master_fod";

        $index = 0;
        $multi_sql = '';
        $error_sql = '';
        
        if (!empty($updateData) ) {
            
            foreach ($updateData as $item ) {

                $Item_Code = $item['Item_Code'];
                $Material_Code = $item['Material_Code'];
                $NumInk = $item['NumInk'];
                $Ink = $item['Ink'];
                $Print_Type = $item['Print_Type'];
                $Cut_Type = $item['Cut_Type'];
                $Fold_Type = $item['Fold_Type'];
                $Dry = $item['Dry'];
                $Heat = $item['Heat'];
                $Finish_Length = $item['Finish_Length'];
                
                $Finish_Width = $item['Finish_Width'];
                $RemarkTop = $item['RemarkTop'];
                $RemarkBot = $item['RemarkBot'];
                $RemarkJobJacket = $item['RemarkJobJacket'];
                $Printing_Speed = $item['Printing_Speed'];
                $Updated_By = $item['Updated_By'];
                $Updated = $item['Updated'];

                // Xử lý update code vật tư mới
                    $sql = "SELECT `Item_Code` FROM $table WHERE `Item_Code`='$Item_Code' ORDER BY ID DESC LIMIT 1;";
                    $query = mysqli_query($conn, $sql);
                    if (!$query ) {
                        $error_sql .= "$index. Error: $sql ; <br />\n";
                    } else {
                        if (mysqli_num_rows($query) > 0 ) {
                            $multi_sql .= " UPDATE 
                                                $table 
                                            SET 
                                                `Material_Code`='$Material_Code', 
                                                `NumInk`='$NumInk', 
                                                `Ink`='$Ink', 
                                                `Print_Type`='$Print_Type', 
                                                `Cut_Type`='$Cut_Type', 
                                                `Fold_Type`='$Fold_Type', 
                                                `Dry`='$Dry', 
                                                `Heat`='$Heat', 
                                                `Finish_Length`='$Finish_Length', 
                                                `Finish_Width`='$Finish_Width', 
                                                `RemarkTop`='$RemarkTop', 
                                                `RemarkBot`='$RemarkBot', 
                                                `RemarkJobJacket`='$RemarkJobJacket', 
                                                `Printing_Speed`='$Printing_Speed', 
                                                `Updated_By`='$Updated_By', 
                                                `Updated`='$Updated'
                                            WHERE 
                                                `Item_Code`='$Item_Code' ; ";
                            $countCheck++;
                        } else {
                            $multi_sql .= "INSERT INTO $table                                             
                                            (
                                                `Item_Code`, 
                                                `Material_Code`, 
                                                `NumInk`, 
                                                `Ink`, 
                                                `Print_Type`, 
                                                `Cut_Type`, 
                                                `Fold_Type`, 
                                                `Dry`, 
                                                `Heat`, 
                                                `Finish_Length`, 

                                                `Finish_Width`, 
                                                `RemarkTop`, 
                                                `RemarkBot`, 
                                                `RemarkJobJacket`,
                                                `Printing_Speed`,
                                                `Updated_By`,
                                                `Updated`
                                            ) VALUES (
                                                '$Item_Code',
                                                '$Material_Code',
                                                '$NumInk',
                                                '$Ink',
                                                '$Print_Type',
                                                '$Cut_Type',
                                                '$Fold_Type',
                                                '$Dry',
                                                '$Heat',
                                                '$Finish_Length',

                                                '$Finish_Width',
                                                '$RemarkTop',
                                                '$RemarkBot',
                                                '$RemarkJobJacket',
                                                '$Printing_Speed',
                                                '$Updated_By',
                                                '$Updated'
                                            );";
                            $countCheck++;
                        }
                        
                    }
            }
        }

        if (!empty($multi_sql)) {

            if(!mysqli_multi_query($conn, $multi_sql) ) {
                $message = "Update Material error "; // . mysqli_error($conn);
            } else {
                do{
                    // Store first result set
                    if ($result=mysqli_store_result($conn)) {
                        // Free result set
                        mysqli_free_result($result);
                    }
                }while (mysqli_next_result($conn));

                $message = "Update Material Success. Updated: $countCheck";
            }

        } else {
            $message = "Item does not exist in the Master Data (Empty) ";
        }
        // close db
            mysqli_close($conn);
        // result
            return $message;
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    window.location="./";
</script>
