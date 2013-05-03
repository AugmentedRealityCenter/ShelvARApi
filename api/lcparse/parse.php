<?php 
  /** \file
   * \author Bo Brinkman
   * \author Eliot Fowler
   * \copyright All rights reserved
   * \brief A wrapper for parseToAssocArray. 
   * \details This handles converting JSON to an array to pass to the parser, and then
   * re-wrapping the returned associative array as JSON to pass back to the caller.
   * \param[in] callNumInput
   * The callNumInput POST variable should contain one string, which
   * represents the call number to be parsed. Specifically, we expect
   * an object with one member. The member's name should be "lcNum"
   * and the value should be the actual call number. Note that any
   * slashes or back-slashes in the string will be stripped, so this
   * method should be compatible with jQuery's .post method. Example input:
   *"{"lcNum" : "AB121.12 .A45 2000 A65"}"
   * \return
   * A JSON object that contains the information held in the parsedArr
   * variable. TODO document the members of this object.
   * \note Used by librariantagger.js, book_sleuth, book_seen
   */

include("parseLibrary.php");
include_once('../api_ref_call.php');

$JSONin = stripslashes($_POST["callNumInput"]);
$JSONin = json_decode($JSONin,true);

echo json_encode(parseToAssocArray($JSONin["call_number"]));

?>

