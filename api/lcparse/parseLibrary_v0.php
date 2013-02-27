<?php
include_once ("result.php");
include_once ("conflict.php");
/**
* @file
* \author Bo Brinkman
* \author Eliot Fowler
* \copyright All rights reserved
* \brief Parses the LC Call Number passed to 10 fields and then outputs it as
* an associative array.
*
* \b Error \b Causes:
* \arg Alphabetic portion doesn't exist
* \arg Alphabetic portion longer than three characters
* \arg Whole class portion doesn't exist
* \arg Whole class portion longer than four numbers
* \arg Space before the whole class portion
* \arg Decimal portion longer than four numbers
* \arg Space before the decimal portion
* \arg First Date field indicates ebook
* \arg First Date field contains other alphabetic characters
* \arg First Cutter doesn't exist
* \arg First Cutter has more than five numbers
* \arg First Cutter doesn't contain any numerical portion
* \arg First Cutter has trailing letters
* \arg First Cutter indicates ebook
* \arg First Cutter doesn't contain the alphabetic portion
* \arg Second Date field indicates ebook
* \arg Second Date field contains other alphabetic characters
* \arg Second Cutter has more than five numbers
* \arg Second Cutter has trailing letters
* \arg Second Cutter indicates ebook
* \arg All fields empty
* \arg All fields full
* \arg Extra characters found (other than ".", " ")
* \arg Ends with something other than a number or letter (such as a period)
* \arg Trailing and leading whitespace
*
* \b Warning \b Causes:
* \arg The fields element 8, element 9, and element 10 are not empty (currently ShelvAR does not support those fields)
* \arg First Cutter doesnt't contain a period
*
*
* \b Output:
* An associative array with the following 10 fields:
* \arg alphabetic
* \arg wholeClass
* \arg decClass
* \arg date1
* \arg cutter1
* \arg date2
* \arg cutter2
* \arg element8
* \arg element9
* \arg element10
*
*
* @see http://www.oclc.org/bibformats/en/0xx/050.shtm
*
* @version March 18, 2012
* \note Used by librariantagger.js, book_sleuth, book_seen
*/

/**
* Array of size variables
**/
$GLOBALS['arrOfSizes']=null;
/**
* Variable that keeps track of spaces before each field (except the first)
**/
$GLOBALS['arrOfSpaces']=null;

/**
* Result object
**/
$GLOBALS['res']=null;

function initialize()
{
	global $arrOfConflicts, $arrOfSizes, $arrOfSpaces, $res;

	$arrOfConflicts = array();

	$res = new result();
	$res->endResult["allow"] = true;
	$res->endResult["warningFree"] = true;

	$arrOfSizes = array('alphabeticSize' => 0, 'wholeClassSize' => 0, 'decClassSize' => 0, 'date1Size' => 0, 'cutter1Size' => 0, 'date2Size' => 0,
						'cutter2Size' => 0, 'element8Size' => 0, '$element9Size' => 0, 'element10Size' => 0);

	$arrOfSpaces = array('spacesBeforeWhole' => 0, 'spacesBeforeDec' => 0, 'spacesBeforeDate1' => 0, 'spacesBeforeCut1' => 0, 'spacesBeforeDate2' => 0, 'spacesBeforeCut2' => 0,
						'spacesBeforeEle8' => 0, 'spacesBeforeEle9' => 0, 'spacesBeforeEle10' => 0);
}

/**
* Takes a string as input and attempts to parse the call number
*
* Multiple methods are called
* in order to parse a string into the 10 fields of a call number.
* The way the string is parsed is by keeping track of how many characters
* were in each of the previous sections, how many spaces there have been, and
* by using the guidelines previously described.
*
* @param lcNum
* The input string,
*
* @return
* An associative array that contains the information held in the parsedArr variable.
*
**/
function parseToAssocArray_delegate($lcNum)
{
	global $arrOfConflicts, $res, $parsedArr;

        initialize();

        $lcnumi = $lcNum;
        $lcNum = trim_excess_whitespace($lcnumi);
        if(strcmp($lcnumi, $lcNum) != 0) {
           //We trimmed, should add warning.
           addConflictTrimmedWhitspace();
        }

	$parsedArr["alphabetic"] = strtoupper(getAlphabetic($lcNum));
	$parsedArr["wholeClass"] = getWholeClass($lcNum);
	$parsedArr["decClass"] = getDecClass($lcNum);
	$parsedArr["date1"] = strtoupper(getDate1($lcNum));
	$parsedArr["cutter1"] = strtoupper(getCutter1($lcNum));
	$parsedArr["date2"] = strtoupper(getDate2($lcNum));
	$parsedArr["cutter2"] = strtoupper(getCutter2($lcNum));
	$parsedArr["element8"] = strtoupper(getElement8($lcNum));
	$parsedArr["element8meaning"] = getElement8Meaning($parsedArr["element8"]);
	$parsedArr["element9"] = strtoupper(getElement9($lcNum));
	$parsedArr["element10"] = strtoupper(getElement10($lcNum));

    fixDateFields();

	checkForBugs($lcNum);

	$res->endResult["lcNum"] = $parsedArr;
	$res->endResult["parserVersion"] = 0;
	$res->endResult["originalInput"] = $lcNum;
	$res->endResult["arrOfConflicts"] = $arrOfConflicts;

	return $res->endResult;
}
/**
* Parses the alphabetic portion of the LC number
**/
function getAlphabetic($lcNum)
{
	global $arrOfSizes;

	$chars = 0;
	$loc = 0;
	$cur = substr($lcNum, 0, 1);
	while(ctype_alpha($cur) && $loc < strlen($lcNum))
	{
		$chars++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}


	$arrOfSizes['alphabeticSize'] = $chars;

	$result = substr($lcNum, 0, $chars);

        if($chars > 3)
	{
		addConflictAlphaTooBig();
	}

	else if($chars == 0)
	{
		addConflictAlphaExistence();
	}

	return $result;
}
/**
* Parses the whole number part of the class portion of the call number
**/
function getWholeClass($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$loc = $arrOfSizes['alphabeticSize'] + $arrOfSpaces['spacesBeforeWhole'];
	$cur = substr($lcNum, $loc, 1);
        $spaceBeforeWhole = false;

	if($cur == " ")
	{
            $spaceBeforeWhole = true;
            $arrOfSpaces['spacesBeforeWhole']++;
            $loc++;
            $cur = substr($lcNum, $loc, 1);
	}

	$numSize = 0;

	while(is_numeric($cur) && $loc < strlen($lcNum))
	{
            $numSize++;
            $chars++;
            $loc++;
            $cur = substr($lcNum, $loc, 1);
	}

	$arrOfSizes['wholeClassSize'] = $chars;

        if($spaceBeforeWhole)
            addConflictSpaceBeforeWhole();

	if($numSize > 4)
	{
            addConflictWholeNumbersTooMany();
	}

	$result = substr($lcNum, $arrOfSizes['alphabeticSize'] + $arrOfSpaces['spacesBeforeWhole'], $chars);

	if($chars == 0)
        {
            addConflictWholeExistence();
            return "";
        }

	return $result;
}
/**
* Parses the decimal number part of the class portion of the call number
**/
function getDecClass($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'];
	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	//Either not dec class or not correct
	//
	//We should never leave the if statement and stay in the function
	//without at least returning an error.
	if($cur == " " && substr($lcNum, $loc+1, 1) == ".")
	{
		$arrOfSpaces['spacesBeforeDec']++;
		//if we are truely in the decimal portion and not the cutter
		if(is_numeric(substr($lcNum, $loc+2, 1)))
		{
                    $loc++;
                    $cur = substr($lcNum, $loc, 1);
		}
		//if we are in the cutter portion
		else if(ctype_alpha(substr($lcNum, $loc+2, 1)))
		{
			return "";
		}
	}
	if($cur == ".")
	{
		$chars++;
		$loc++;
		$numSize = 0;
		if(!is_numeric(substr($lcNum, $loc, 1)))
		{
			//should probably send an error here
			return "";
		}

		while(is_numeric(substr($lcNum, $loc, 1)) && $loc < strlen($lcNum))
		{
			$numSize++;
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}
		$arrOfSizes['decClassSize'] = $chars;

                if($arrOfSpaces['spacesBeforeDec'] > 0)
                    addConflictSpaceBeforeDecimal();

		if($numSize > 3)
		{
			addConflictDecNumsTooMany();
		}

		$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeDec'] + 1, $arrOfSizes['decClassSize'] - 1); //+ and - 1 are for the decimal

		return $result;
	}

	return "";
}
/**
* Parses the first date of the call number
**/
function getDate1($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$date1numeric = 0;

	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'];

	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	if($cur == " " && is_numeric(substr($lcNum, $loc+1, 1)))
	{
		$loc++;
		$cur = substr($lcNum, $loc, 1);
		$arrOfSpaces['spacesBeforeDate1']++;
		while(is_numeric($cur))
		{
			$date1numeric++;
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}
		if($chars > 0)
		{
			while(ctype_alpha($cur))
			{
				$chars++;
				$loc++;
				$cur = substr($lcNum, $loc, 1);
			}
		}
	}
	if($chars > 1)
		$arrOfSizes['date1Size']= $chars;
	else {
            $arrOfSizes['date1Size']= 0;
            return "";
        }

	$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeDate1'], $arrOfSizes['date1Size']);
	/*
	//check ebook
	if(strtolower(substr($result, $date1numeric)) == "eb")
		addConflictIsEbook1();

	else if($arrOfSizes['date1Size'] > $date1numeric)
		addConflictDate1HasAlpha();

    if(intval($result) > 4095)
		addConflictDate1TooLong();
	*/
	return $result;


}
/**
* Parses the first cutter number of the call number
**/
function getCutter1($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;
	$chars = 0;
	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'];
	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

        $cutter1MissingPeriod = false;

	if($cur == " ")
	{
		$arrOfSpaces['spacesBeforeCut1']++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}
	if($cur == "." || ctype_alpha($cur))
	{
		if($cur == '.'){
			//accounting for the decimal
			$arrOfSpaces['spacesBeforeCut1']++;
			$loc++;
		} else {
            $cutter1MissingPeriod = true;
		}
		if(ctype_alpha(substr($lcNum, $loc, 1)))
		{
			$chars++; //accounting for the initial letter
			$loc++;
			$cur = substr($lcNum, $loc, 1);
			$numCount = 0;
			while(is_numeric($cur) && $loc < strlen($lcNum))
			{
				$numCount++;
				$chars++;
				$loc++;
				$cur = substr($lcNum, $loc, 1);
			}
			while((ctype_alpha($cur) || is_numeric($cur)) && $loc < strlen($lcNum)){
				$chars++;
				$loc++;
				$cur = substr($lcNum, $loc, 1);
			}

			$arrOfSizes['cutter1Size']= $chars;

            if($numCount > 5)
			{
				addConflictTooManyCutter1NumsError();
			}
			if($numCount == 0)
				addConflictNoNumsInCutter1Num();

                        if($cutter1MissingPeriod)
                            addConflictCutter1MissingPeriod();

			$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeCut1'], $chars);

			if($numCount != $chars-1){
                if(strtolower(substr($result,-2)) == 'eb'){
					addConflictCutter1IndicatesEbook();
                }	else if(strtolower(substr($result,-1)) != 'x') {
						addConflictCutter1HasTrailingLetters();
					}
			}
			if($chars == 0)
				return "";

			return $result;
		}
		else {
			addConflictCutter1MissingLetter();
		}
	}

	return "";
}
/**
* Parses the second date of the call number
**/
function getDate2($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;

	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1'];
	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);
	$date2numeric = 0;

	if($cur == " " && is_numeric(substr($lcNum, $loc+1, 1)))
	{
		$loc++;
		$cur = substr($lcNum, $loc, 1);
		$arrOfSpaces['spacesBeforeDate2']++;
		while(is_numeric($cur))
		{
			$date2numeric++;
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}
		if($chars > 0)
		{
			while(ctype_alpha($cur))
			{
				$chars++;
				$loc++;
				$cur = substr($lcNum, $loc, 1);
			}
		}

	}
	if($chars > 1)
		$arrOfSizes['date2Size']= $chars;
	else {
		$arrOfSizes['date2Size']= 0;
                return "";
        }

	$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeDate2'], $arrOfSizes['date2Size']);
	/*
	//check ebook
	if(strtolower(substr($result, -2)) == "eb")
		addConflictIsEbook2();

	if($date2numeric < $arrOfSizes['date2Size']){
		addConflictDate2HasAlpha();
	}

	if(intval($result) > 4095)
		addConflictDate2TooLong();
		*/
	return $result;
}
/**
* Parses the second cutter number of the call number
**/
function getCutter2($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size'] + $arrOfSizes['date2Size'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1'] + $arrOfSpaces['spacesBeforeDate2'];
	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	if($cur == " ")
	{
		$arrOfSpaces['spacesBeforeCut2']++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}

	if($cur == ".")
	{
		//@AB no reason cutter 2 should have period; throw error
		addConflictPeriodInCutter2Num();
//		$arrOfSpaces['spacesBeforeCut2']++; //Does this fix the problem? RED FLAG RED FLAG RED FLAG! WHAT IS THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//		$loc++;
//		$cur = substr($lcNum, $loc, 1);
	}

	if(preg_match("/^[A-Z]$/i", $cur))
	{
		//TODO: This gives weird results if one enters a bad cutter like "pp44"
		while(ctype_alpha($cur))
		{
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}
		if($chars > 1) {
			addConflictTooManyCutter2AlphasError($chars);
		}
		$numCount = 0;
		while(is_numeric($cur) && $loc < strlen($lcNum))
		{
			$numCount++;
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}
		while((ctype_alpha($cur) || is_numeric($cur)) && $loc < strlen($lcNum)){
			$chars++;
			$loc++;
			$cur = substr($lcNum, $loc, 1);
		}

		$arrOfSizes['cutter2Size'] = $chars;

                if($numCount > 5)
			addConflictTooManyCutter2NumsError();

		$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeCut2'], $chars);

		if($numCount != $arrOfSizes['cutter2Size'] - 1){
			if(strtolower(substr($result,-2)) == 'eb'){
				addConflictCutter2IndicatesEbook();
			}
            else if(strtolower(substr($result,-1)) != 'x') {
				addConflictCutter2HasTrailingLetters($numCount);
				}
		}
        if($numCount == 0)
            addConflictNoNumsInCutter2Num();

		if($chars == 0)
			return "";

		return $result;
	}

	return "";
}
/**
* Parses the 8th element of the call number
**/
function getElement8($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1'] + $arrOfSpaces['spacesBeforeDate2'] + $arrOfSpaces['spacesBeforeCut2'];

	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	if($cur == " ")
	{
		$arrOfSpaces['spacesBeforeEle8']++;
		$loc++;
	}

	$cur = substr($lcNum, $loc, 1);

	while((preg_match("/^[a-z]$/i", $cur) || is_numeric($cur)) && $loc < strlen($lcNum))
	{
		$chars++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}

	$arrOfSizes['element8Size'] = $chars;

	if($cur == '.')
	{
		$loc++;
		$cur = substr($lcNum, $loc, 1);

		if($cur == " ")
		{
			$arrOfSpaces['spacesBeforeEle8']++;
			$chars++; //account for period
			$arrOfSizes['element8Size'] = $chars;
		}
	}

	$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeEle8'], $chars);

	if($chars == 0)
		return "";

	/* else if(getElement8Meaning($result) == 'year')
	{
		if(intval($result) > 4095)
			addConflictDate3TooBig();
		if(strtoupper(substr($result,4,2)) == 'EB')
			addConflictDate3isEbook();
	}
	else addConflictElement8NotDate3(); */



	return $result;
}
/**
* Parses the 8th element of the call number
**/
function getElement9($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;

        $origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + $arrOfSizes['element8Size'] + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1'] + $arrOfSpaces['spacesBeforeDate2'] + $arrOfSpaces['spacesBeforeCut2'] + $arrOfSpaces['spacesBeforeEle8'];
        $loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	if($cur == " ")
	{
		$arrOfSpaces['spacesBeforeEle9']++;
		$loc++;
	}

	$cur = substr($lcNum, $loc, 1);

	while((preg_match("/^[a-z]$/i", $cur) || is_numeric($cur)) && $loc < strlen($lcNum))
	{
		$chars++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}

	$arrOfSizes['element9Size']= $chars;

	$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeEle9'], $chars);

	if($chars == 0)
		return "";

	return $result;
}
/**
* Parses the 8th element of the call number
**/
function getElement10($lcNum)
{
	global $arrOfSizes, $arrOfSpaces;

	$chars=0;
	$origLoc = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + $arrOfSizes['element8Size'] + $arrOfSizes['element9Size']+ $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1'] + $arrOfSpaces['spacesBeforeDate2'] + $arrOfSpaces['spacesBeforeCut2'] + $arrOfSpaces['spacesBeforeEle8'] + $arrOfSpaces['spacesBeforeEle9'];

	$loc = $origLoc;

	$cur = substr($lcNum, $loc, 1);

	if($cur == " ")
	{
		$arrOfSpaces['spacesBeforeEle10']++;
		$loc++;
	}

	$cur = substr($lcNum, $loc, 1);

	while($loc < strlen($lcNum))
	{
		$chars++;
		$loc++;
		$cur = substr($lcNum, $loc, 1);
	}
	$arrOfSizes['element10Size']= $chars;

	$result = substr($lcNum, $origLoc + $arrOfSpaces['spacesBeforeEle10'], $chars);

	if($chars == 0)
		return "";

	return $result;
}

function trim_excess_whitespace($str) {
   $ro = preg_replace('/\s+/', ' ', $str);
   return $str;
}

// @cond ASDF
/**
* Adds conflict if alphabetic portion doesn't exist
**/
function addConflictAlphaExistence()
{
	global $arrOfConflicts, $res;

	$problem = new conflict();
	$problem->msg = "The alphabetic portion of the call number doesn't appear to exist";
	$problem->isWarning = false;
	$problem->conflictStart = 0;
	$problem->conflictEnd = 0;

	$arrOfConflicts[] = $problem;
	$res->endResult["allow"] = false;
	$res->endResult["warningFree"] = false;
}
/**
* Adds conflict if alphabetic portion too big
**/
function addConflictAlphaTooBig()
{
	global $arrOfConflicts, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The alphabetic portion of this call number seems to be longer than three letters.";
	$problem->isWarning = false;
	$problem->conflictStart = 0;
	$problem->conflictEnd = $arrOfSizes['alphabeticSize'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if whole portion doesn't exist
**/
function addConflictWholeExistence()
{
	global $arrOfConflicts, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The whole number portion of the call number doesn't appear to exist";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'];
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["allow"] = false;
	$res->endResult["warningFree"] = false;
}

/**
* Adds conflict if space found before whole number
**/
function addConflictSpaceBeforeWhole()
{
	global $arrOfConflicts, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "There appears to be a space before the whole number";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'];
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
        $res->endResult["allow"] = false;
}
/**
* Adds conflict if too many whole numbers
**/
function addConflictWholeNumbersTooMany()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "Your whole number portion of the call number is longer than the allowed four numbers";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['spacesBeforeWhole'];
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['wholeClassSize'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if space found before decimal number
**/
function addConflictSpaceBeforeDecimal()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "There appears to be a space before the decimal number";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize'];
	$problem->conflictEnd = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize'];

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
        $res->endResult["allow"] = false;
}
/**
* Adds conflict if too many decimal numbers
**/
function addConflictDecNumsTooMany()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "Your decimal number portion of the call number is longer than the allowed three numbers";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec'] + 1;
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['decClassSize'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if ebook in date1
**/
function addConflictIsEbook1()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "This appears to be an e-book, based on the \"eb\" in the first date field.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + ($arrOfSizes['date1Size']- 2) + $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1'];
	$problem->conflictEnd = $problem->conflictStart + 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date1 has alpha
**/
function addConflictDate1HasAlpha()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "Alphabetic characters not currently supported in date 1. Date must be a 4-digit year.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + 4 + $arrOfSpaces['spacesBeforeWhole'] + $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1'];
	$problem->conflictEnd = $problem->conflictStart + ($arrOfSizes['date1Size']- 4);

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date1 too long
**/
function addConflictDate1TooLong()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The first date field is larger than 4095.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']
							+ $arrOfSpaces['spacesBeforeDate1'];
	$problem->conflictEnd = $problem->conflightStart + $arrOfSizes['date1Size']- 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter1 doesn't exist
**/
function addConflictCutter1Existence()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The first cutter number of the call number doesn't appear to exist";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1'];
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["allow"] = false;
	$res->endResult["warningFree"] = false;
}
/**
* Adds conflict if cutter1 missing period
**/
function addConflictCutter1MissingPeriod()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "It appears that the period that is supposed to precede the first cutter is missing. We have added it. If this is incorrect, revise your data entry.";
	$problem->isWarning = true;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']- 1;
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
}
/**
* Adds conflict if too many nums in cutter1
**/
function addConflictTooManyCutter1NumsError()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res, $problem;

	$problem = new conflict();
	$problem->msg = "It appears that you have 6 or more numbers in your first cutter number and the maximum allowed is 3.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1'];
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['cutter1Size']- 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if no nums in cutter1
**/
function addConflictNoNumsInCutter1Num()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The first cutter number has no numerical portion";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1'];
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if trailing letters in cutter1
**/
function addConflictCutter1HasTrailingLetters()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The only trailing letter currently supported in the first cutter is \"x\".";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ ($arrOfSizes['cutter1Size']- 1) +
								$arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1'];
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter1 indicates ebook
**/
function addConflictCutter1IndicatesEbook()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "This appears to be an e-book, based on the \"eb\" in the first cutter field.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + ($arrOfSizes['cutter1Size']- 2) + $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+  $arrOfSpaces['spacesBeforeCut1'];
	$problem->conflictEnd = $problem->conflictStart + 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter1 missing letter
**/
function addConflictCutter1MissingLetter()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "Cannot decode this call number. Was expecting a cutter, but cutters must start with a letter.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSpaces['spacesBeforeWhole']
							+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']- 1;
	$problem->conflictEnd = $problem->conflictStart;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date2 indicates ebook
**/
function addConflictIsEbook2()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "This appears to be an e-book, based on the \"eb\" in the second date field.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ ($arrOfSizes['date2Size']- 2) + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']
							+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2'];
	$problem->conflictEnd = $problem->conflictStart + 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date2 has alphabetic characters
**/
function addConflictDate2HasAlpha()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "Alphabetic characters not currently supported in date 2. Date must be a 4-digit year.";
	$problem->isWarning = false;

	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ (4) + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1'] + $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2'];
	$problem->conflictEnd = $problem->conflictStart + ($arrOfSizes['date2Size']- 4);

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date2 too long
**/
function addConflictDate2TooLong()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The second date field is larger than 4095.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']
							+ $arrOfSpaces['spacesBeforeDate2'] + 1;
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['date2Size'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter2 has too many nums
**/
function addConflictTooManyCutter2NumsError()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res, $problem;

	$problem = new conflict();
	$problem->msg = "It appears that you have 6 or more numbers in your second cutter number and the maximum allowed is 5.";
	$problem->isWarning = true;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ $arrOfSizes['date2Size']+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']
							+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['cutter2Size'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter2 has trailing letters
**/
function addConflictCutter2HasTrailingLetters($numSize)
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The only trailing letter currently supported in the first cutter is \"x\".";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ $arrOfSizes['date2Size']+ ($arrOfSizes['cutter2Size'] - $numSize) + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']
							+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
	$problem->conflictEnd = $problem->conflictStart + $numSize;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter2 indicates ebook
**/
function addConflictCutter2IndicatesEbook()
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "This appears to be an e-book, based on the \"eb\" in the second cutter field.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']
							+ ($arrOfSizes['cutter2Size'] - 2) + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']
							+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
	$problem->conflictEnd = $problem->conflictStart + 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter2 has no numbers
**/
function addConflictNoNumsInCutter2Num()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "The second cutter number has no numerical portion";
    $problem->isWarning = false;
    $problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']
							+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']
							+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
    $problem->conflictEnd = $problem->conflictStart + $arrOfSizes['cutter2Size'] - 1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if cutter2 has a period
**/
function addConflictPeriodInCutter2Num()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "The second cutter number should not contain a period";
    $problem->isWarning = false;
    $problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']
							+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']
							+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
    $problem->conflictEnd = $problem->conflictStart + $arrOfSizes['cutter2Size'] - 1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if all fields empty
**/
function addConflictAllFieldsEmpty()
{
    global $arrOfConflicts, $res;

    $problem = new conflict();
    $problem->msg = "ShelvAR has detected that all elements of this call number are empty.";
    $problem->isWarning = false;
    $problem->conflictStart = 0;
    $problem->conflictEnd = 0;

    $arrOfConflicts[] = $problem;
    $res->endResult["allow"] = false;
    $res->endResult["warningFree"] = false;
}
/**
* Adds conflict if extra characters found
**/
function addConflictExtraCharFound($curChar, $loc)
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "character '" . $curChar . "' not allowed";
    $problem->isWarning = false;
    $problem->conflictStart = $loc;
    $problem->conflictEnd = $loc;

    $res->endResult["allow"] = false;
    $res->endResult["warningFree"] = false;

    $arrOfConflicts[] = $problem;
}
/**
* Adds conflict if all fields full
**/
function addConflictAllFieldsFull()
{
    global $arrOfConflicts, $arrOfSizes, $res;

    $numSize = 0;
    foreach($arrOfSizes as $curSize)
    {
        $numSize += $curSize;
    }

    $problem = new conflict();
    $problem->msg = "ShelvAR has detected that all elements of this call number are full. If this is not a mistake, please disregard this message.";
    $problem->isWarning = true;
    $problem->conflictStart = 0;
    $problem->conflictEnd = $numSize-1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
}
/**
* Adds conflict if date3 too big
**/
function addConflictDate3TooBig()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

	$problem = new conflict();
	$problem->msg = "The third date field is larger than 4095.";
	$problem->isWarning = false;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size'] + $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size']
							+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec'] + $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']
							+ $arrOfSpaces['spacesBeforeDate2'] + $arrOfSpaces['spacesBeforeCut2'] + $arrOfSpaces['spacesBeforeEle8'] + 1;
	$problem->conflictEnd = $problem->conflictStart + $arrOfSizes['element8size'] - 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}
/**
* Adds conflict if date3 indicates ebook
**/
function addConflictDate3isEbook()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "This appears to be an e-book, based on the \"eb\" after the year in field 8. ShelvAR recommends not printing a label for this volume.";
    $problem->isWarning = false;
    $problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + ($arrOfSizes['element8Size'] - 2) + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2']+ $arrOfSpaces['spacesBeforeEle8'];
    $problem->conflictEnd = $problem->conflictStart + 1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if last char not alphabetic or numeric
**/
function addConflictLastCharIsNotAlphaNumeric()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "The last number in the call number is not a number or a letter.";
    $problem->isWarning = false;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if first char is a space
**/
function addConflictFirstCharIsSpace()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "ShelvAR has detected that there are spaces leading the call number you entered. Please remove them.";
    $problem->isWarning = false;
    $problem->conflictStart = 0;
    $problem->conflictEnd = 0;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if last cahr is a space
**/
function addConflictLastCharIsASpace($loc)
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "Please remove all trailing spaces.";
    $problem->isWarning = false;
    $problem->conflictStart = $loc-1;
    $problem->conflictEnd = $loc-1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
    $res->endResult["allow"] = false;
}
/**
* Adds conflict if element8 not date3
**/
function addConflictElement8NotDate3()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "Field 8 is currently unused, unless it contains a year, between 0 and 4095.";
    $problem->isWarning = true;
    $problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2']+ $arrOfSpaces['spacesBeforeEle8'];
    $problem->conflictEnd = $problem->conflictStart + $arrOfSizes['element8Size'] - 1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
}
/**
* Adds conflict if ele9 and ele10 not empty
**/
function addConflictElements9and10NotEmpty()
{
    global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res;

    $problem = new conflict();
    $problem->msg = "ShelvAR does not currently support LC Call Numbers that make use of the 9th and 10th fields of the call number standards and has not included them in the parsing.";
    $problem->isWarning = true;
    $problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']+ $arrOfSizes['date2Size']+ $arrOfSizes['cutter2Size'] + $arrOfSizes['element8Size'] + $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']+ $arrOfSpaces['spacesBeforeDate2'] + $arrOfSpaces['spacesBeforeCut2']+ $arrOfSpaces['spacesBeforeEle8'] + $arrOfSpaces['spacesBeforeEle9'];
    $problem->conflictEnd = $problem->conflictStart + $arrOfSizes['element9Size']+ $arrOfSizes['element10Size']+ $arrOfSpaces['spacesBeforeEle10'] - 1;

    $arrOfConflicts[] = $problem;
    $res->endResult["warningFree"] = false;
}

function addConflictTooManyCutter2AlphasError($cutter2Alphs)
{
	global $arrOfConflicts, $arrOfSpaces, $arrOfSizes, $res, $problem;

	$problem = new conflict();
	$problem->msg = "It appears that you have more than 1 alphabetic character in your second cutter number.";
	$problem->isWarning = true;
	$problem->conflictStart = $arrOfSizes['alphabeticSize'] + $arrOfSizes['wholeClassSize']+ $arrOfSizes['decClassSize'] + $arrOfSizes['date1Size']+ $arrOfSizes['cutter1Size']
							+ $arrOfSizes['date2Size']+ $arrOfSpaces['spacesBeforeWhole']+ $arrOfSpaces['spacesBeforeDec']+ $arrOfSpaces['spacesBeforeDate1']+ $arrOfSpaces['spacesBeforeCut1']	+ $arrOfSpaces['spacesBeforeDate2']+ $arrOfSpaces['spacesBeforeCut2'];
	$problem->conflictEnd = $problem->conflictStart + $cutter2Alphs + 1;

	$arrOfConflicts[] = $problem;
	$res->endResult["warningFree"] = false;
	$res->endResult["allow"] = false;
}

function addConflictTrimmedWhitspace() {
   global $arrOfConflicts, $res;

   $problem = new conflict();
   $problem->msg = "There were extra spaces that we trimmed.";
   $problem->isWarning = true;
   $problem->conflictStart = 0;
   $problem->conflictEnd = 0;

   $arrOfConflicts[] = $problem;
   $res->endResult["warningFree"] = false;
   $res->endResult["allow"] = true;
}

/**
* Checks for remaining bugs on whole LC
**/
function checkForBugs($lcNum)
{
	global $arrOfSizes, $parsedArr;

	//All fields empty
        $allEmpty = true;
	foreach ($arrOfSizes as $curSize) {
            if($curSize > 0)
                $allEmpty = false;
        }
        if($allEmpty)
            addConflictAllFieldsEmpty();

	//Extra characters
	$count = 0;
	$curChar = 0;
	while($count < strlen($lcNum)-1)
	{
		$curChar = substr($lcNum, $count, 1);
		if(!ctype_alpha($curChar) && !is_numeric($curChar) && $curChar != " " && $curChar != '.')
                    addConflictExtraCharFound($curChar, $count);
		$count++;
	}

	//All fields full
	$allFull = true;
	foreach ($arrOfSizes as $curSize) {
            if($curSize == 0)
                $allFull = false;
        }
        if($allFull)
		addConflictAllFieldsFull();

    //Last Character is not Alpha-Numeric
	if(!is_numeric(substr($lcNum, strlen($lcNum)-1, 1)) && !ctype_alpha(substr($lcNum, strlen($lcNum)-1, 1)) && strlen($lcNum) > 0)
		addConflictLastCharIsNotAlphaNumeric();

	//First character is a space
	if(substr($lcNum, 0, 1) == " ")
            addConflictFirstCharIsSpace();

	//Last character is a space
	if(substr($lcNum, strlen($lcNum)-1, 1) == " ")
		addConflictLastCharIsASpace(strlen($lcNum));

    //Element 9 and 10 are not empty
	if($arrOfSizes['element9Size']!= 0 || $arrOfSizes['element10Size']!= 0)
		addConflictElements9and10NotEmpty();

	//check ebook
	if(strtolower(substr($parsedArr['date1'], -2)) == "eb")
		addConflictIsEbook1();

	if(!is_numeric($parsedArr['date1']) && $parsedArr['date1'] != null)
		addConflictDate1HasAlpha();

	if(intval($parsedArr['date1']) > 4095)
		addConflictDate1TooLong();

	//check ebook
	if(strtolower(substr($parsedArr['date2'], -2)) == "eb")
		addConflictIsEbook2();

	if(!is_numeric($parsedArr['date2']) && $parsedArr['date2'] != null)
		addConflictDate2HasAlpha();

	if(intval($parsedArr['date2']) > 4095)
		addConflictDate2TooLong();

	if(getElement8Meaning($parsedArr['element8']) == 'year') {
		//check ebook
		if(strtolower(substr($parsedArr['element8'], -2)) == "eb")
			addConflictDate3isEbook();

		if(intval($parsedArr['element8']) > 4095)
			addConflictDate2TooLong();
	}
	else if($arrOfSizes['element8Size'] > 0)
		addConflictElement8NotDate3();
}
// @endcond
/**
* Checks meaning of element8
**/
function getElement8Meaning($e8){
	if(is_numeric($e8)){
		$val = intval($e8);
		if($val >= 0 && $val < 4096)	{
			return 'year';
		}
	}

	$nums = 0;
	$loc = 0;
	$cur = substr($e8, $loc, 1);
	while(is_numeric($cur) && $loc < strlen($e8)) {
		$nums++;
		$loc++;
		$cur = substr($e8, $loc, 1);
	}

	if(substr($e8, 0, $nums) > 0 && substr($e8, 0, $nums) < 4096) {
		if(strtolower(substr($e8, $nums)) == 'x' ||
			strtolower(substr($e8, $nums)) == 'b' ||
			strtolower(substr($e8, $nums)) == 'ax') {
				return 'year';
			}
	}


	//default return value
	return 'unknown';
}

function fixDateFields() {
    global $arrOfSizes, $parsedArr;

    if($arrOfSizes['date1Size'] == 0 && $arrOfSizes['date2Size'] == 0)
        return;

    if($arrOfSizes['date2Size'] != 0)
    {
        if($arrOfSizes['cutter2Size'] == 0 && $arrOfSizes['element8Size'] == 0)
        {
            //move date2 to date3
            $arrOfSizes['element8Size'] = $arrOfSizes['date2Size'];
            $parsedArr["element8"] = $parsedArr["date2"];
            $parsedArr["element8meaning"] = 'year';
            $arrOfSizes['date2Size'] = 0;
            $parsedArr["date2"] = null;
        }
    }
    if($arrOfSizes['date2Size'] == 0 && $arrOfSizes['date1Size'] > 0) {
        if($arrOfSizes['cutter1Size'] == 0) {
            if($arrOfSizes['cutter2Size'] == 0 && $arrOfSizes['element8Size'] == 0) {
                //move date1 to date3
                $arrOfSizes['element8Size'] = $arrOfSizes['date1Size'];
                $parsedArr["element8"] = $parsedArr["date1"];
                $parsedArr["element8meaning"] = 'year';
                $arrOfSizes['date1Size'] = 0;
                $parsedArr["date1"] = null;
            }
            else {
                //move date1 to date2
                $arrOfSizes['date2Size'] = $arrOfSizes['date1Size'];
                $parsedArr["date2"] = $parsedArr["date1"];
                $arrOfSizes['date1Size'] = 0;
                $parsedArr["date1"] = null;
            }

        }
    }
}
