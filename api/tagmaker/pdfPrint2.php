<?php
require_once('helper/fpdf.php');
require_once('../lc2bin/lc_numbers_lib.php');
include_once "../HammingCode.php";

/** GLOBAL VARS **/
//array of callNumbers to print
$tagsParam = json_decode($_GET['tags']);
//requested sheet type
$sheetTypeParam = $_GET['type'];
		
//grab the different label options and put them in $sheetValues
$paper_format = fetchOptions(urldecode($sheetTypeParam));

$pdf = new FPDF($paper_format->orientation,$paper_format->units,array($paper_format->width,$paper_format->height));

$tags_per_page = how_many_per_page($paper_format);
$tag_chunks = array_chunk($tagsParam,$tags_per_page);

for($i=0; $i < count($tag_chunks); $i++){
  make_page($pdf,$paper_format,$tag_chunks[$i]);
 }

$pdf->Output( ($paper_format->name . ".pdf"), "I");

function make_page($pdf,$paper_format,$tags){
  $pdf->AddPage();
  make_logo($pdf,$paper_format);

  $start_x = $paper_format->margin_left;
  $inc_x = $paper_format->label_width + $paper_format->hspace;
  $end_x = $paper_format->width - $paper_format->margin_right - $paper_format->label_width;
  
  $start_y = $paper_format->margin_top;
  $inc_y = $paper_format->label_height + $paper_format->vspace;
  $end_y = $paper_format->height - $paper_format->margin_bottom - $paper_format->label_height;

  $tag_index = 0;

  for($y=$start_y; $y <= $end_y; $y += $inc_y){
    for($x=$start_x; $x <= $end_x; $x += $inc_x){
      if($tag_index < count($tags)){
	make_tag($x,$y,$pdf,$paper_format,$tags[$tag_index]);
      }
      $tag_index += 1;
    }
  }
}

function make_tag($x, $y, $pdf, $paper_format, $tag){
  $pdf->SetDrawColor(127);
  $pdf->Rect($x,$y,$paper_format->label_width,$paper_format->label_height);

  $safety_buffer = $paper_format->tag_width / 11.0;

  $code_y = $y + $paper_format->label_height - $safety_buffer;
  $code_x = $x + ($paper_format->label_width - $paper_format->tag_width)/2.0;
  $pdf->Rect($x,$y-$paper_format->tag_width,$paper_format->tag_width, $paper_format->tag_width);
}

//Note: The x and y are of the LOWER LEFT corner, and you are to 
// print upwards from there, returning the height printed
function make_code($x, $y, $pdf, $paper_format, $tag){

  return 0;
}

function how_many_per_page($paper_format){
  $adj_width = $paper_format->width - $paper_format->margin_left - $paper_format->margin_right + $paper_format->hspace;
  $tags_wide = round($adj_width/($paper_format->label_width+$paper_format->hspace));

  $adj_height = $paper_format->height - $paper_format->margin_top - $paper_format->margin_bottom + $paper_format->vspace;
  $tags_tall = round($adj_height/($paper_format->label_height+$paper_format->vspace));

  return $tags_wide*$tags_tall;
}
		
/**
 * Grab the available label sheet options
 **/
function fetchOptions($paper_type){
  $tempValues = file_get_contents('tagformats.json');
  $json_arr = json_decode($tempValues);
  foreach($json_arr as $options){
    if($options->name === $paper_type){
      if($options->orientation === "L"){
	//Need to rotate everything
	$temp = $options->margin_left;
	$options->margin_left = $options->margin_top;
	$options->margin_top = $options->margin_right;
	$options->margin_right = $options->margin_bottom;
	$options->margin_bottom = $temp;

	$temp = $options->label_width;
	$options->label_width = $options->label_height;
	$options->label_height = $temp;
	
	$temp = $options->hspace;
	$options->hspace = $options->vspace;
	$options->vspace = $temp;

	$temp = $options->width;
	$options->width = $options->height;
	$options->height = $temp;
      }

      return $options;
    }
  }

  return null;
}
		
/***
 * Make the ShelvAR logo
 *     31x13
 ***/
function make_logo($pdf,$paper_format){
  $pdf->SetDrawColor(0);

  $logoArr = array(array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
		   array(1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1),
		   array(1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1),
		   array(1,0,1,0,0,0,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,1,1,0,0,0,1,0,1),
		   array(1,0,1,0,1,1,1,0,0,0,1,0,0,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1),
		   array(1,0,1,0,0,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,0,0,1,0,0,1,1,0,1),
		   array(1,0,1,1,0,0,1,0,1,0,1,0,0,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1),
		   array(1,0,1,1,1,0,1,0,1,0,1,0,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1),
		   array(1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,1,0,0,1,1,0,1,0,1,0,1,0,1,0,1),
		   array(1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1),
		   array(1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1),
		   array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1)
		   );
  
  if($paper_format->margin_top > $paper_format->margin_left){
    $scale = $paper_format->margin_top / 12.0;
    for($i=0; $i < 12; $i++){
      for($j=0; $j < 31; $j++){
	if(1 == $logoArr[$i][$j]){
	  $pdf->Rect(11*$scale +($j * $scale),
		     ($i * $scale),
		     $scale,
		     $scale,
		     'F'); //  X, Y, W, H, Fill 
	}
      }
    }
  } else {
    $scale = $paper_format->margin_right / 12.0;
    for($i=0; $i < 12; $i++){
      for($j=0; $j < 31; $j++){
	if(1 == $logoArr[$i][$j]){
	  $pdf->Rect(($i * $scale),
		     (11+30)*$scale - ($j * $scale),
		     $scale,
		     $scale,
		     'F'); //  X, Y, W, H, Fill 
	}
      }
    }
  }
}

?>