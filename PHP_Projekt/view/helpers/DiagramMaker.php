<?php

namespace view\helpers;

class DiagramMaker
{

	//färger som ska användas i diagram. i ordning.
	private static $colors = array(
		array(213,42,87), //röd
		array(87,213,42), //grön
		array(42,87,213), //blå
		array(213,167,42), //gul
		array(213,98,42), //orange
		array(167,42,213), //rosa
		array(42,213,167), //turkos
		array(55,15,79), //lila
		array(113,60,22), //brun
		array(125,146,126) //grå
		);

	public static function getDiagramColors()
	{
		return self::$colors;
	}

	public static function drawCircleDiagram($dataArr, $width, $height)
	{


		$image = imagecreatetruecolor($width, $height);

	    //bilden har som default svart bakgrund. här görs den om till genomskinlig.
	    imagesavealpha($image, true); //spara data om genomskinliga delar i bilden.
	    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127); //"genomskinlig färg"
	    imagefill($image, 0, 0, $alpha); //gör hela bilden genomskinlig

		$startPoint = 0;
		$endPoint = 0;
		for($i = 0; $i < count($dataArr); $i++)
		{
			//hoppar över ifall ett svar inte har några röster (0%)
			if($dataArr[$i] === 0)
			{
				continue;
			}

			$color = imagecolorallocate($image, self::$colors[$i][0], self::$colors[$i][1], self::$colors[$i][2]);

			$endPoint = $startPoint + 360 * $dataArr[$i];  

			imagefilledarc($image, $width/2, $height/2, $width, $height, $startPoint, $endPoint, $color, IMG_ARC_PIE);

			$startPoint = $endPoint;
		}
	    
	    
		ob_start();
        imagepng($image);
        $raw = ob_get_clean();
        return '<img class="diagramImage" src="data:image/png;base64,'.base64_encode( $raw).'">';
	}


}