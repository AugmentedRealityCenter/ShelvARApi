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

  $code_top = make_code($code_x, $code_y, $y, $pdf, $paper_format, $tag);
  $num_top = -1;
  if($code_top >= 0){
    //This 2.0/72 is to make some space between the tag and the lc
    $num_top = make_num($code_x, $code_top-(2.0/72), $y, $pdf, $paper_format, $tag);
  }

  if($code_top < 0 || $num_top < 0){
    //TODO print error message
    error_log("Tag too tall");
  }
}

//Note: The x and y are of the LOWER LEFT corner, and you are to 
// print upwards from there, returning the $y coordinate of the top.
// Should not print if tag won't fit between $bottom and $top, return
// error code instead. Any negative value is an error.
function make_num($left, $bottom, $top, $pdf, $paper_format, $tag){
  $pdf->SetFont('Courier','B',8);
  $pdf->SetTextColor(0);
  
  $lc_string = tag_to_lc($tag);
  $lc_parts = array_filter(explode(" ",$lc_string), 'strlen');

  $processed_parts = array();
  $parts_index = 0;
  while($parts_index < count($lc_parts)){
    if($pdf->GetStringWidth($lc_parts[$parts_index]) > $paper_format->tag_width){
      //Next part needs to be split. TODO
    }
    $joined = $lc_parts[$parts_index];
    if(count($processed_parts) > 0){
      $joined = $processed_parts[count($processed_parts)-1] . " " . $joined;
    }
    if($pdf->GetStringWidth($joined) <= $paper_format->tag_width){
      $processed_parts[count($processed_parts)-1] = $joined;
    } else {
      $processed_parts[] = $lc_parts[$parts_index];
    }
    $parts_index++;
  }

  $lines_tall = count($lc_parts);
  $new_top = $bottom - (8.0/72)*$lines_tall;
  if($new_top < $top) {
    //Oops, not enough room
    return -1;
  }

  $lc_toprint = implode("\n",$lc_parts);
  $multi_shift = 3.0/72;
  $pdf->SetXY($left-$multi_shift,$new_top);
  $pdf->MultiCell(0,(8.0/72),$lc_toprint,0);

  return $new_top;
}

//Note: The x and y are of the LOWER LEFT corner, and you are to 
// print upwards from there, returning the $y coordinate of the top.
// Should not print if tag won't fit between $bottom and $top, return
// error code instead. Any negative value is an error.
function make_code($left, $bottom, $top, $pdf, $paper_format, $tag){
  $tag_bin = base642bin($tag);
  if(strlen($tag_bin) < 7) return -1;
  $tag_size_type = decode_7_4(substr($tag_bin,0,7));
  if(strlen($tag_size_type) != 4) return -2;

  //Base tag height is 25, including border rows and type/size row.
  // Each added block adds 9 rows
  $tag_rows_high = 25 + (9 * bindec(substr($tag_size_type, 2, 4)));

  $rect_size = $paper_format->tag_width / 11.0;

  $code_height = $rect_size*$tag_rows_high;
  $top_after = $bottom - $code_height;
  if($top_after < $top){
    return -3;
  }

  $pdf->SetDrawColor(0);
  $pdf->SetFillColor(0);

  //Print outer border
  for($x=0;$x<11;$x++){
    $pdf->Rect($left + $rect_size*$x,$top_after,$rect_size,$rect_size,"F");
    $pdf->Rect($left + $rect_size*$x,$bottom-$rect_size,$rect_size,$rect_size,"F");
  }
  for($y=0;$y<$tag_rows_high;$y++){
    $pdf->Rect($left,$top_after+$rect_size*$y,$rect_size,$rect_size,"F");
    $pdf->Rect($left+$paper_format->tag_width-$rect_size,$top_after+$rect_size*$y,$rect_size,$rect_size,"F");
  }

  $tag_pos = 0;
  $tag_arr = str_split($tag_bin);
  for($y=($tag_rows_high-4)-1; $y>=0; $y--){
    for($x=0; $x<11-4; $x++){
      if($tag_arr[$tag_pos] == '1'){
	$pdf->Rect($left + $rect_size*($x+2), $top_after+$rect_size*($y+2),$rect_size,$rect_size,"F");
      }
      $tag_pos++;
    }
  }

  return $top_after;
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