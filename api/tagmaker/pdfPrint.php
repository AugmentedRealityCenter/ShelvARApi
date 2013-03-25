<?php
//EXAMPLE INPUT
//		http://devapi.shelvar.com/tagmaker/9003/%5B%22NX543%20.c38%202000%22%2C%22NX543%20.c38%202000%22%5D.json
//   JSON array (w/o HTML encoding):  ["NX543%20.c38%202000","NX543%20.c38%202000"]
//test URL: http://devapi.shelvar.com/tagmaker/9003/%5B%22NX543%20.c38%202000%22%2C%22NX543%20.c38%202000%22%5D.json
	
		require_once('helper/fpdf.php');
		require_once('../lc2bin/LC_Converter_lib.php');
	
		/** GLOBAL VARS **/
		//conversion rate from inches to millimeters
		$convertInchToMillimeter = 25.4;
		//array of call nums in base 64
		$binCallNums = array();
		//array of callNumbers to print
		$callNumsParam = json_decode(stripslashes($_GET['nums']), true);
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
		$binCallNums = MultLC2Bin($callNumsParam);
		//print_r($b64CallNums); //$$$DEBUG
		
		
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
		//for every number / tag to be created...
		$binStr = implode($binCallNums[0]);
		//@TODO FINISH set tag properties
		$tagWidth = 7; //width in blocks
		$tagHeight = ceil(strlen($binStr) / $tagWidth); //height in blocks
		$blockSize = 1.5; //width/height of block in millimeters
		$x = 1;
		$y = $tagHeight;
		//@TODO big loop to do multiple tags
		//FOR EVER BINARY STRING IN THE ARRAY...
		$j = 0;
			for($i=0;$i<strlen($binStr);$i++){
				//loop through, printing each tag from bottom left to top right
				if(0 == strcmp("1", substr($binStr, $i, 1))){
					$pdf->Rect($x*$blockSize,
								$y*$blockSize,
								$blockSize,
								$blockSize,
								'F'); //  X, Y, W, H, Fill 
				}
				$x++;
				if($x > $tagWidth){
					$x = 1;
					$y--;
				}
			}
			//print LC num below tag
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY(0, $tagHeight + 15);
			$pdf->MultiCell($tagWidth * 2, 2, $callNumsParam[$j]);
		//}
		
		
		/**
		  * Grab the available label sheet options
		**/
		function fetchOptions(){
			global $sheetValues;
			$header = array();
			$tempValues = file("http://devapi.shelvar.com/tagmaker/csvFormats", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
		
	$pdf->Output("output.pdf", "D");
?>