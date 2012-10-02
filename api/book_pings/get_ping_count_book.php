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
$count = 0;
$sql = "SELECT * FROM book_pings WHERE ";
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
    $qArray[] = 'book_call = ?'; 
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



function refValues($arr){ 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
        return $refs; 
} 


	// Create a new mysqli object with database connection parameters
	mysqli_report(MYSQLI_REPORT_ALL);
	$mysqli = new mysqli($server, $user, $password, $database);

	if(mysqli_connect_errno()) {
		echo "Connection Failed: " . mysqli_connect_errno();
		exit();
	}

	if($count == 0){
		Print "FAILED 1";
		//Print 'SQL Select failed' . mysqli_error();
	} else if(mysqli_num_rows($result) == 0) {
		Print "NO ROWS";
		//Print 'No rows seleted';
	} else {
		//Print $resource;
		echo "hi";
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		$count = count($arr);
		// Close connection
		$con -> close();
		return $count;
	}*/
?>