<?
/**
* @file
* Implements functionality for result objects
*
* @version October 9, 2011
* @author Eliot Fowler
*/
include_once('../api_ref_call.php');
/**
* Defines a result
**/
class result {
	/**
	* The JSON-encoded result being returned
	**/
	public $endResult = array("allow" => true, "warningFree" => true);
	/**
	* The JSON formatted call number
	**/
	public $lcNum;
	/**
	* True if no errors (can have warnings)
	**/
	public $allow;
	/**
	* True if no warnings
	**/
	public $warningFree;
	/**
	* Array that contains all conflicts
	**/
	public $arrOfConflicts;
	
	function __construct() {
		$arrOfConflicts = array();
		$allow = true;
		$warningFree = true;
		$endResult["allow"] = $allow;
		$endResult["warningFree"] = $warningFree;
		$endResult["arrOfConflicts"] = $arrOfConflicts;
	}

	function __construct1($jsonEncoded) {
		$lcNum = $jsonEncoded;
		$allow = false;
		$warningFree = false;
	}

	function addConflict($newConflict, $isWarning)
	{
		$this->arrOfConflicts[] = $newConflict;
		
		$this->allow = !$isWarning;
		$this->warningFree = !$isWarning;
	}
	
	function getLcNum()
	{
		return $this->lcNum;
	}
	
	function setLcNum($jsonEncoded)
	{
		$this->lcNum = $jsonEncoded;
		//json_decode($this->lcNum);
	}
}
?>