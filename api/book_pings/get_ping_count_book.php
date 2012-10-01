<?php
include_once ("../../db_info.php");

class BindParam{ 
    private $values = array(), $types = ''; 
    
    public function add( $type, &$value ){ 
        $this->values[] = $value; 
        $this->types .= $type; 
    } 
    
    public function get(){ 
        return array_merge(array($this->types), $this->values); 
    } 
}

$bindParam = new BindParam(); 
$qArray = array();
$count = -1;
$sql = "SELECT * FROM book_pings WHERE";
$result;

if(isset($_GET["book_tag"]))
	$book_tag = $_GET['book_tag'];

if(isset($_GET["call_number"]))
	$call_number = $_GET['call_number'];

if(isset($_GET["start_date"]))
	$start_date = $_GET['start_date'];

if(isset($_GET["end_date"]))
	$end_date = $_GET['end_date'];

if(isset($_GET["book_tag"])){ 
	$qArray[] = 'book_tag = ?'; 
	$bindParam->add('s', $_GET["book_tag"]); 
} 
if(isset($_GET["call_number"])){ 
    $qArray[] = 'call_number = ?'; 
    $bindParam->add('s', $_GET["call_number"]); 
} 
if(isset($_GET["start_date"])){ 
	$qArray[] = 'start_date = ?'; 
	$bindParam->add('s', $_GET["start_date"]); 
} 
if(isset($_GET["end_date"])){ 
    $qArray[] = 'end_date = ?'; 
    $bindParam->add('s', $_GET["end_date"]); 
} 

$sql .= implode(' AND ', $qArray); 

echo $sql . '<br/>'; 
var_dump($bindParam->get()); /*

	// Create a new mysqli object with database connection parameters
	$con = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}
	echo "book_tag is " . $book_tag . " and book call is " . $call_number;
	// Create a prepared statement
		if($stmt = $con -> prepare($sql)) {
			// Bind parameters
			 //s - string, b - blob, i - int, etc
			$stmt -> bind_param('ss', $book_tag, $call_number);

			//Execute it
			$stmt -> execute();

			// Bind results
			$stmt -> bind_result($result);

			// Fetch the value
			$stmt -> fetch();

			// Close statement
			$stmt -> close();
		}


	if($result == FALSE){
		Print "FAILED 1";
		//Print 'SQL Select failed' . mysqli_error();
	} else if(mysqli_num_rows($result) == 0) {
		Print "NO ROWS";
		//Print 'No rows seleted';
	} else {
		//Print $resource;
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		$count = count($arr);
		// Close connection
		$con -> close();
		return $count;
	}
	*/
?>