<?php 

/** \file
 * \author Bo Brinkman
 * \author Eliot Fowler
 * \copyright All rights reserved
 * \brief This method is just a wrapper which allows the user to select which
 * version of the parseLibrary to use.
 * \note Used by librariantagger.js, book_sleuth, book_seen
 **/
 include_once('../api_ref_call.php');
 
function parseToAssocArray($lcNum, $version=0)
{		
	switch($version){
		case 0:
		default:
			include_once('parseLibrary_v0.php');
	}
	$delegateres = parseToAssocArray_delegate($lcNum);
	
	return $delegateres;
}

