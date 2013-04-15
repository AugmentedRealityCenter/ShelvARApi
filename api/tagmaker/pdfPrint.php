<?php
//EXAMPLE INPUT
//		http://devapi.shelvar.com/tagmaker/9003/%5B%22NX543%20.c38%202000%22%2C%22NX543%20.c38%202000%22%5D.json
//   JSON array (w/o HTML encoding):  ["NX543%20.c38%202000","NX543%20.c38%202000"]
//test URL: http://devapi.shelvar.com/tagmaker/9003/%5B%22NX543%20.c38%202000%22%2C%22AB453%20.c38%202000%22%2C%22ZX732%20.c38%202000%22%2C%22AB453%20.c38%202000%22%2C%22ZX732%20.c38%202000%22%2C%22AB453%20.c38%202000%22%2C%22ZX732%20.c38%202000%22%2C%22AB453%20.c38%202000%22%2C%22ZX732%20.c38%202000%22%5D.json
//   cd /var/www/html/shelvar_devapi/api/tagmaker/pdfPrint.php
	
		require_once('helper/fpdf.php');
		require_once('../lc2bin/lc_numbers_lib.php');
		include_once "../HammingCode.php";
	
		/** GLOBAL VARS **/
		//conversion rate from inches to millimeters
		$convertInchToMillimeter = 25.4;
		//width in blocks (11 columns + 2 on both sides for borders)
		$tagWidth = 15; 
		//array of call nums in base 64
		$binCallNums = array();
		//array of callNumbers to print
		$tagsParam = json_decode($_GET['tags']);
		//requested sheet type
		$sheetTypeParam = $_GET['type'];
		//array of sheet values
		$sheetValues = array();
		//the chosen sheet type
		$sheetType = array();
		
		//grab the different label options and put them in $sheetValues
		fetchOptions();
		
		//search $sheetValues for the selected sheet type
		for($i=0;$i<sizeof($sheetValues);$i++){
			if(false !== strpos($sheetValues[$i]['name'], $sheetTypeParam)){
				$sheetType = $sheetValues[$i];
			}
		}
		//print_r($sheetType); //$$$DEBUG
		
		//grab all base64 conversions for call numbers
		//also, grab the height from the first one (assumes all are uniform height)
		//### ABOVE WORKS ###
		$returnedHeighOfTag = decode_7_4( substr( base642bin($tagsParam[0]) ,0,7) );
		for($i=0; $i < sizeof($tagsParam); $i++){
			$binCallNums[$i] = base642bin($tagsParam[$i]);
		}
		
		//use FPDF to print the tag, use assoc. array global var as params
		$pdf = new FPDF( ($sheetType['isVert'] ? 'P':'L') //P for portrait/vert, L for landscape/horiz 
						 ); 
		//if in english units, convert all units to millimeters
		if(0 == strcmp("TRUE", $sheetType['isEnglishUnits'])){
			$sheetType['paper width'] = $sheetType['paper width']  * $convertInchToMillimeter;
			$sheetType['paper height'] = $sheetType['paper height']  * $convertInchToMillimeter;
			$sheetType['marginT'] = $sheetType['marginT']  * $convertInchToMillimeter;
			$sheetType['marginL'] = $sheetType['marginL']  * $convertInchToMillimeter;
			$sheetType['marginB'] = $sheetType['marginB']  * $convertInchToMillimeter;
			$sheetType['marginR'] = $sheetType['marginR']  * $convertInchToMillimeter;
			$sheetType['tag width'] = $sheetType['tag width']  * $convertInchToMillimeter;
			$sheetType['tag height'] = $sheetType['tag height']  * $convertInchToMillimeter;
			$sheetType['spacebetweenH'] = $sheetType['spacebetweenH']  * $convertInchToMillimeter;
			$sheetType['spacebetweenV'] = $sheetType['spacebetweenV']  * $convertInchToMillimeter;
		}
		
		/**** 
		    ACTUALLY MAKE THE PDF 
		****/
		$pdf->AddPage();
		$tagHeight = (25 + 9 * bindec(substr($returnedHeighOfTag, 2, 4)) + 5); //height in blocks from WS + 5 for border
		$blockSize = 1.5; //width/height of block in millimeters
		//calc number of tags that can fit across
		$numAcrossPage = floor($sheetType['paper width'] / ($tagWidth + 5));
		//how many rows down the printer is
		$rowOffset = 0;
		//for every number / tag to be created...
		for($j=0;$j<sizeof($binCallNums);$j++){
			$binStr = $binCallNums[$j];
			$tagBlockIndex = 0;
			//fix tag spacing based on chosen label type and page-print them
			$xOffset = 1 + (($tagWidth + 2) * ($j%$numAcrossPage) ) + $sheetType['marginL'];
//2			$xOffset = 1 + (($tagWidth + 2) * $j) + $sheetType['marginL'];
			$x = $xOffset;
			$yOffset = $tagHeight * (floor($j / $numAcrossPage) + 1) + (4 * floor($j/$numAcrossPage)) + $sheetType['marginT'];
//2			$yOffset = $tagHeight + $sheetType['marginT'];
//			print_r("numAcross: " . $numAcrossPage . " || yOffset: " . $yOffset);
			$y = $yOffset;
			$tagIndex = 0;
			
			//if it's off the right side of the page (including margins) move it to the next row
//2			print_r("{ j: " . $j . " xOffset:" . $xOffset . " , compare: " . ($sheetType['paper width'] - $sheetType['marginR'] - $sheetType['marginL'] - $tagWidth - 2) . " }");
//2			if($xOffset >= ($sheetType['paper width'] - $sheetType['marginR'] - $sheetType['marginL'] - $tagWidth - 2) ){
//2				$rowOffset += ($tagHeight + $sheetType['spacebetweenV'] + 4);
//2				$xOffset = floor($xOffset / $sheetType['paper width'] - $sheetType['marginR'] - $sheetType['marginL'] - $tagWidth - 2);
//2			}
			//apply the number of rows we've moved down
//2			$yOffset += $rowOffset;
			for($i=0;$i<$tagHeight*$tagWidth;$i++){
				//loop through, printing each block in the tag from bottom left to top right (including border)
				//if it's 1 above/below the bottom and sides, fill it in (outer border)
				if( ($y == ($yOffset - $tagHeight)+1) || ($y == $yOffset) || ($x == $xOffset) || ($x == ($xOffset + $tagWidth - 1)) ){
					$pdf->Rect($x*$blockSize,
								$y*$blockSize,
								$blockSize,
								$blockSize,
								'F'); //  X, Y, W, H, Fill 
				}else if( !(($y == ($yOffset - $tagHeight+2)) || ($y == $yOffset-1) || ($x == $xOffset+1) || ($x == ($xOffset + $tagWidth-2))) ){
					//else if it's NOT 2 above/below the bottom and sides, don't fill it in and advance the encoder tracker
					if(0 == strcmp("1", substr($binStr, $tagIndex, 1))){
						//print a black square
						$pdf->Rect($x*$blockSize,
									$y*$blockSize,
									$blockSize,
									$blockSize,
									'F'); //  X, Y, W, H, Fill 
						$tagBlockIndex++; //move to next tag block index
					}
					$tagIndex++;
				}
				//no matter what, advance this
				$x++;
				if($x > ($xOffset + $tagWidth-1)){
					$x = $xOffset;
					$y--;
				}
			}
			//print LC num below tag
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($xOffset + (8 * ($j%$numAcrossPage+1)), 
					$yOffset + (24 * (floor($j/$numAcrossPage)+1)));
			$pdf->MultiCell($tagWidth * 2, 2, tag_to_lc($tagsParam[$j]));
		}
		
		
		/**
		  * Grab the available label sheet options
		**/
		function fetchOptions(){
			global $sheetValues;
			$header = array();
			$tempValues = file('tagformats.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$header = explode(",", $tempValues[0]);
			
			for($i=1;$i<sizeof($tempValues);$i++){
				$temp = explode(",", $tempValues[$i]);
				$tempIndex = array($header[0]=>$temp[0]);
				if("" != $temp[0]){
					for($j=1;$j<sizeof($temp);$j++){
						$add = (array($header[$j]=>$temp[$j]));
						$tempIndex = array_merge( $tempIndex, $add );
					}
					array_push($sheetValues, $tempIndex);
				}
			}
		}
		
	$pdf->Output( ($sheetType['name'] . ".pdf"), "I");
?>