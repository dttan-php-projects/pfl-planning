<?php 
    $img = "B (" . rand(1,63) . ").jpg";
    header('Content-Type: image/jpeg');
    readfile($img);
 ?>