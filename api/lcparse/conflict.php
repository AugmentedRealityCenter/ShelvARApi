<?
/**
* @file
* Implements functionality for conflict objects
*
* There are two different types of conflicts:
*
* Warnings which occur when an incorrectly formatted LC Call Number was entered
* but will still allow the number to parse and errors which occur when an LC Call 
* Number was entered that was unable to be parsed and nothing will be returned
*
* \b Warnings \b include:
* \arg Alphabetic portion not existing
* \arg All fields empty
* \arg 
*
* @version September 26, 2011
* @author Eliot Fowler
*/

include_once('../api_ref_call.php');

class conflict {
	/**
	* True if the conflict is a warning and NOT an error
	**/
	public $isWarning;
	/**
	* Start character of the conflict in the given LC Num
	**/
	public $conflictStart;
	/**
	* End character of the conflict in the given LC Num
	**/
	public $conflictEnd;
	/**
	* English msg of problem
	**/
	public $msg;

	function __construct2() {
		$isWarning = false;
		$conflictStart = -1;
		$conflictEnd = -1;
		$msg = "";
	}

	function __construct3($warning, $strtVal, $endVal) {
		$isWarning = false;
		$conflictStart = $startVal;
		$conflictEnd = $endVal;
		$msg = "";
	}

	function set_msg_no_alpha()
	{
		$msg = "alphabetic portion doesn't exist";
	}
}
?>