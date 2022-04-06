<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ("./vendor/autoload.php"); 
    require_once ('../Module/Database.php');
    $conn = _conn1();
    $table = "pfl_item_master";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";
    $message = "Not Submit";
    $multi_sql = '';
    
    $sql = "SELECT * FROM $table WHERE `Material_Code` LIKE '% %' ORDER BY ID ASC ;";
    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0 ) {
        $data = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $count=0;
        foreach ($data as $key => $value ) {
            $Material_Code = trim($value['Material_Code']);
            $Item_Code = trim($value['Item_Code']);
            if (!empty($Material_Code ) ) {
                $sql = " UPDATE $table SET `Material_Code`='$Material_Code' WHERE `Item_Code`='$Item_Code'; ";
                // $multi_sql .= " UPDATE $table SET `Material_Code`='$Material_Code' WHERE `Item_Code`='$Item_Code'; ";
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    $message = "Error SQL: $sql";
                    break;
                } else {
                    $message = "Update Material success ";
                    $count++;
                }
                
            }
        }

        // update 
            if ($result ) {
                $message = "$message. Count: $count ";
            }
        
    }


?>
<script>
    var message = '<?php  echo $message; ?>';
    alert(message);
    window.location="../";
</script>
