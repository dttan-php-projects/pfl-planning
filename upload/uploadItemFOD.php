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

            $file_name = 'Item_FOD_' . $_SERVER['REMOTE_ADDR'] . '_' . $Updated_By . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
                $createArray = array('Item_Code', 'Remark' );
                $makeArray = array( 'Item_Code' => 'Item_Code', 'Remark' => 'Remark' );
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
                            $Remark = $SheetDataKey['Remark']; 
                        
                        // get data 
                            $Item_Code = filter_var(trim(strtoupper($allDataInSheet[$i][$Item_Code]) ), FILTER_SANITIZE_STRING);
                            $Remark = filter_var(trim($allDataInSheet[$i][$Remark]), FILTER_SANITIZE_STRING);

                        
                        // check empty data
                            if (empty($Item_Code)) {
                                $checkPause++;
                                if ($checkPause == 2 ) break;
                                continue;
                            } 
                        
                        // get data
                            $updateData[] = array( 
                                'Item_Code' => $Item_Code,
                                'Remark' => $Remark,
                                'Updated_By' => $Updated_By,
                                'Updated' => $Updated
                                
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
        $table = "pfl_new_item_fod";

        $index = 0;
        $multi_sql = '';
        $error_sql = '';
        
        if (!empty($updateData) ) {
            
            foreach ($updateData as $item ) {

                $Item_Code = $item['Item_Code'];
                $Remark = $item['Remark'];
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
                                                `Remark`='$Remark', 
                                                `Updated_By`='$Updated_By', 
                                                `Updated`='$Updated'
                                            WHERE 
                                                `Item_Code`='$Item_Code' ; ";
                            $countCheck++;
                        } else {
                            $multi_sql .= "INSERT INTO $table (`Item_Code`, `Remark`, `Updated_By`, `Updated`) VALUES ('$Item_Code', '$Remark', '$Updated_By', '$Updated');";
                            $countCheck++;
                        }
                        
                    }
            }
        }

        if (!empty($multi_sql)) {

            if(!mysqli_multi_query($conn, $multi_sql) ) {
                $message = "Update Item FOD error "; // . mysqli_error($conn);
            } else {
                do{
                    // Store first result set
                    if ($result=mysqli_store_result($conn)) {
                        // Free result set
                        mysqli_free_result($result);
                    }
                }while (mysqli_next_result($conn));

                $message = "Update Item FOD Success. Updated: $countCheck";
            }

        } else {
            $message = "No data is updated";
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
