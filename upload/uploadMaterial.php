<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ("./vendor/autoload.php"); 
    require_once ('../Module/Database.php');
    $conn = _conn1();
    $table = "pfl_item_master";
    $table_fod = "pfl_item_master_fod";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    $message = "Not Submit";
    if (isset($_POST["submit"])) {

        $allowedFileType = ['application/vnd.ms-excel', 'application/octet-stream', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {

            $file_name = 'PFL_Material_' . $_SERVER['REMOTE_ADDR'] . '_' . $updateBy . '_' . date('Y-m-d_H-i-s') . '.xlsx';
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
                $createArray = array( 'GLID', 'Current_Material_Code', 'New_Material_Code' );
                $makeArray = array( 'GLID' => 'GLID', 'Current_Material_Code' => 'Current_Material_Code', 'New_Material_Code' => 'New_Material_Code' );
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
            
            // load data
                if ($flag == 1) {
                    
                    for ($i = 2; $i <= count($allDataInSheet); $i++) {
                        // get col key
                            $GLID = $SheetDataKey['GLID']; 
                            $Current_Material_Code = $SheetDataKey['Current_Material_Code']; 
                            $New_Material_Code = $SheetDataKey['New_Material_Code']; 
                        
                        // get data 
                            $Item_Code = filter_var(trim(strtoupper($allDataInSheet[$i][$GLID]) ), FILTER_SANITIZE_STRING);
                            $Current_Material_Code = filter_var(trim($allDataInSheet[$i][$Current_Material_Code]), FILTER_SANITIZE_STRING);
                            $New_Material_Code = filter_var(trim($allDataInSheet[$i][$New_Material_Code]), FILTER_SANITIZE_STRING);
                        
                        // check empty data
                            if (empty($Item_Code) || empty($Current_Material_Code) || empty($New_Material_Code) ) continue;
                        
                        // get data
                            $updateData[] = array( 'Item_Code' => $Item_Code, 'Current_Material_Code' => $Current_Material_Code, 'New_Material_Code' => $New_Material_Code );
                    }
                }

                $index = 0;
                $multi_sql = '';
                $multi_sql_fod = '';
                $error_sql = '';
                $error_sql_fod = '';
                if (!empty($updateData) ) {
                    $countCheck = 0;
                    foreach ($updateData as $item ) {

                        $index++;
    
                        $Item_Code = trim($item['Item_Code']);
                        $Material_Code = trim($item['Current_Material_Code']);
                        $New_Material_Code = trim($item['New_Material_Code']);

                        // Thêm vào bảng pfl_item_master_fod: để kiểm tra đơn FOD cho code vật tư mới
                            $sql_fod = "SELECT `Material_Code` FROM $table_fod WHERE `Item_Code`='$Item_Code' ORDER BY ID DESC LIMIT 1;";
                            $query_fod = mysqli_query($conn, $sql_fod);
                            if (!$query_fod ) {
                                $error_sql_fod .= "$index. Error: $sql_fod ; <br />\n";
                            } else {
                                if (mysqli_num_rows($query_fod) > 0 ) {
                                    $multi_sql_fod .= " UPDATE $table_fod SET `Material_Code`='$New_Material_Code', `JobJacket`='' WHERE `Item_Code`='$Item_Code'; ";
                                } else {
                                    $multi_sql_fod .= " INSERT INTO $table_fod (`Item_Code`, `Material_Code`) VALUES ('$Item_Code', '$New_Material_Code'); ";
                                }
                            }

                        // Xử lý update code vật tư mới
                            $sql = "SELECT `Material_Code` FROM $table WHERE `Item_Code`='$Item_Code' AND `Material_Code`='$Material_Code' ORDER BY ID DESC LIMIT 1;";
                            $query = mysqli_query($conn, $sql);
                            if (!$query ) {
                                $error_sql .= "$index. Error: $sql ; <br />\n";
                            } else {
                                if (mysqli_num_rows($query) > 0 ) {
                                    $multi_sql .= " UPDATE $table SET `Material_Code`='$New_Material_Code' WHERE `Item_Code`='$Item_Code' AND `Material_Code`='$Material_Code' ; ";
                                    $countCheck++;
                                } else {
                                    $message = "Item does not exist in the Master Data ";
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

                    // xử lý bảng FOD
                        if (!empty($multi_sql_fod)) {

                            if(!mysqli_multi_query($conn, $multi_sql_fod) ) {
                                $message = "Update Material (FOD) error "; // . mysqli_error($conn);
                            } else {
                                do{
                                    // Store first result set
                                    if ($result=mysqli_store_result($conn)) {
                                        // Free result set
                                        mysqli_free_result($result);
                                    }
                                }while (mysqli_next_result($conn));
                            }

                        }

                } else {
                    $message = "Item does not exist in the Master Data (Empty) ";
                }
            // close db
                mysqli_close($conn);
            
        } else {
            $message = "Invalid File Type. Upload Excel File.";
        }
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    window.location="../";
</script>
