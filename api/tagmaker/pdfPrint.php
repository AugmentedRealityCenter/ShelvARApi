<?php
//EXAMPLE INPUT
//		http://devapi.shelvar.com/tagmaker/9003/%5B%22NX543%20.c38%202000%22%2C%22NX543%20.c38%202000%22%5D.json
//   JSON array (w/o HTML encoding):  ["NX543%20.c38%202000","NX543%20.c38%202000"]
//test URL: http://devapi.shelvar.com/make_tags/9003.pdf?tags=[%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22,%20%22007vO2Yn0xGiT_i7CzD_VU00%22,%20%22001TIi7n9JsKqM_XTwg00000%22]
//   cd /var/www/html/shelvar_devapi/api/tagmaker/pdfPrint.php
	
		require_once('helper/fpdf.php');
		require_once('../lc2bin/lc_numbers_lib.php');
		include_once "../HammingCode.php";
	
		/** GLOBAL VARS **/
		//conversion rate from inches to millimeters
		$convertInchToMillimeter = 25.4;
		//how wide the tag should be in inches
		//$tagWidthInch = 0.375;
		//how wide the tag should be in millimeters
		$tagWidthMilliMeter = 9.525;
		//width in blocks (11 columns + 2 on both sides for borders)
		$tagWidth = 11; 
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
			$sheetType['label width'] = $sheetType['label width']  * $convertInchToMillimeter;
			$sheetType['label height'] = $sheetType['label height']  * $convertInchToMillimeter;
			$sheetType['spacebetweenH'] = $sheetType['spacebetweenH']  * $convertInchToMillimeter;
			$sheetType['spacebetweenV'] = $sheetType['spacebetweenV']  * $convertInchToMillimeter;
		}
		
		/**** 
		    ACTUALLY MAKE THE PDF 
		****/
		$pdf->AddPage();
		//width/height of block in millimeters
		$blockSize = $tagWidthMilliMeter / $tagWidth; 
		//tag height in blocks
		$tagHeight = 25 + (9 * bindec(substr($returnedHeighOfTag, 2, 4))); //height in blocks from WS + 5 for border
		//calc number of tags that can fit across
		$numAcrossPage = ceil(($sheetType['paper width'] - $sheetType['marginL'] - $sheetType['marginR']) / ($tagWidthMilliMeter + $sheetType['spacebetweenH'])) + 1;
		//calc number of tags high the page should be
		$numHighOnPage = ceil(($sheetType['paper height'] - $sheetType['marginT'] - $sheetType['marginB']) / (($blockSize * $tagHeight) + $sheetType['spacebetweenV'])) + 1;
//		print_r("{ " . $sheetType['paper width'] . " , " . $sheetType['marginL'] . " , " . $sheetType['marginR'] . " }");
//		print_r("{ " . $tagWidthMilliMeter . " , " . $sheetType['spacebetweenH'] . " , " . $sheetType['marginR'] . " }");
//		print_r("{ " . $tagWidthMilliMeter . " }");
//		print_r("[ " . sizeof($binCallNums) . " , " . $numAcrossPage . " , " . $numHighOnPage . " ]");
		//how many rows down the printer is
		$rowOffset = 0;
		//for every number / tag to be created...
		for($j=0;$j<sizeof($binCallNums);$j++){
			$binStr = $binCallNums[$j];
			$tagBlockIndex = 0;
			//fix tag spacing based on chosen label type and page-print them
			$xOffset = (($tagWidthMilliMeter + $sheetType['spacebetweenH']) * ($j%$numAcrossPage) ) + $sheetType['marginL'];
			$x = $xOffset;
			//(tag size in mm * num of tags across for rows) + (number of tags * space between them vert) + marginT + (stored tag height - what was used)     ***off by one vert
			$yOffset = ($tagHeight * $blockSize) * (floor($j / $numAcrossPage) + 1) + ((floor($j / $numHighOnPage) + 1) * $sheetType['spacebetweenV']) 
					+ $sheetType['marginT'] + (floor($j / $numAcrossPage) * ($sheetType['label height'] - ($tagHeight * $blockSize)));
			$y = $yOffset;
			$tagIndex = 0;
			
//			print_r("{ " . $j . " , " . $xOffset . " , " . $yOffset . " }");
			
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
				if($x >= ($xOffset + $tagWidth)){
					$x = $xOffset;
					$y--;
				}
			}
			//print LC num below tag
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($x - (($j % $numAcrossPage) * 4.5), 
					$y + ((floor($j / $numHighOnPage)+1) * 22));
					//$y + ((floor($j / $numAcrossPage)+1) * 10));
			$pdf->MultiCell($tagWidth, 2, tag_to_lc($tagsParam[$j]));
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