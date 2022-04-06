<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require("../Module/Database.php");


if (isset($_GET["EVENT"]) && $_GET["EVENT"] == "MASTERDATAGRID") {
  $table = "pfl_item_master";
  $header = '<head>
                    <column width="50" type="ch" align="center" sort="na">#</column>
                    <column width="60" type="ed" align="left" sort="str">No</column>
                    
                    <column width="110" type="ed" align="left" sort="str">Item Code</column>
                    <column width="110" type="ed" align="left" sort="str">Material Code</column>
                    <column width="100" type="ed" align="left" sort="str">Số Mực</column>
                    <column width="100" type="ed" align="left" sort="str">Tên Mực</column>
                    <column width="110" type="ed" align="left" sort="str">Phương Pháp In</column>
                    <column width="110" type="ed" align="left" sort="str">Phương Pháp Cắt</column>
                    <column width="110" type="ed" align="left" sort="str">Phương Pháp Gấp</column>
                    <column width="60" type="ed" align="left" sort="str">Sấy</column>
                    <column width="60" type="ed" align="left" sort="str">Độ Nóng</column>
                    <column width="90" type="ed" align="left" sort="str">Finish Length</column>

                    <column width="80" type="ed" align="left" sort="str">Finish Width</column>
                    <column width="100" type="txt" align="left" sort="str">Ghi Chú Trên</column>
                    <column width="*" type="txt" align="left" sort="str">Ghi Chú Dưới</column>
                    <column width="100" type="txt" align="left" sort="str">Ghi Chú Job</column>
                    <column width="100" type="ed" align="left" sort="str">Tốc Độ In</column>
                    <column width="100" type="ed" align="left" sort="str">Người Cập Nhật</column>
                    <column width="100" type="ed" align="left" sort="str">Ngày Cập Nhật</column>
                    
                    

				</head>';

  header('Content-type: text/xml');
  echo "<rows>";
  echo $header;
  $masterItem = MiQuery("SELECT * FROM $table ORDER BY ID DESC LIMIT 4000;", _conn1());
  $index = 0;
  foreach ($masterItem as $row) {

    $index++;

    echo '<row id="' . str_replace("&", "&amp;", $row['ID']) . '">';
    echo '<cell>0</cell>';
    echo '<cell>' . $index . '</cell>';

    echo '<cell>'. str_replace("&","&amp;",$row['Item_Code']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Material_Code']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['NumInk']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Ink']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Print_Type']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Cut_Type']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Fold_Type']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Dry']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Heat']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Finish_Length']) .'</cell>';

    echo '<cell>'. str_replace("&","&amp;",$row['Finish_Width']) .'</cell>';
    echo '<cell>'. trim(htmlspecialchars($row['RemarkTop'])) .'</cell>';
    echo '<cell>'. trim(htmlspecialchars($row['RemarkBot'])) .'</cell>';
    echo '<cell>'. trim(htmlspecialchars($row['RemarkJobJacket'])) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Printing_Speed']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Updated_By']) .'</cell>';
    echo '<cell>'. str_replace("&","&amp;",$row['Updated']) .'</cell>';


      // foreach ($row as $K => $S) {
      //   echo '<cell>' . str_replace("&", "&amp;", $row[$K]) . '</cell>';
      // }
    echo '</row>';
  }
  echo "</rows>";
}
