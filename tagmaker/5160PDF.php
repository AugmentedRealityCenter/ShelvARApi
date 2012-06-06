<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

/**
 * @file
 * @author Bo Brinkman
 * @date 2011-11-20
 *
 * Generate a PNG image that prints as a sheet of 5160 labels.
 * 
 * @param $tag1 First tag, in base 64 format
 * @param $tag2 ... $tag30 Remaining tags, in base 64 format
 */
require 'fpdf17/fpdf.php';

$pdf = new FPDF('L','in','Letter');
$pdf->AddPage();

//Someday it would be better to use vector-based graphics
$URL = 'http://easlnx01.eas.muohio.edu/~shelvar/release/tagmaker/5160.png?' . $_SERVER['QUERY_STRING'];
$pdf->Image($URL,0,0,11,8.5,'PNG');

$pdf->Output();
?>