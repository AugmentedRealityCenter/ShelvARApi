<?php 
include("parseLibrary.php");

$JSONin = stripslashes($_POST["callNumInput"]);
$JSONin = json_decode($JSONin,true);

echo json_encode(parseToJSON($JSONin["lcNum"]));

?>

