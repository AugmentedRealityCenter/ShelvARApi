<?php 
include("find_book.php");

$JSONin = stripslashes($_POST["callNumInput"]);
$JSONin = json_decode($JSONin,true);

echo json_encode(find_book($JSONin["lcNum"]));

?>

