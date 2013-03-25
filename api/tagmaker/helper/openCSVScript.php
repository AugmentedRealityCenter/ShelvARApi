<?php

$csvArr = file("http://devapi.shelvar.com/tagmaker/tempcsv", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

//the array of CSV values
echo json_encode($csvArr);
?>