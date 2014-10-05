<?php

namespace view\helpers;

class DiagramMaker
{

	private $colors = array(
		array(255,0,0), //red
		array(0,255,0), //green
		array(0,0,255), //blue
		array(255,255,0) //yellow
		);

	public static function getDiagramColors()
	{
		return $this->colors;
	}

	public static function drawCircleDiagram($dataArr, $width, $height)
	{


		$image = imagecreatetruecolor($width, $height);

		$startPoint = 0;
		$endPoint = 0;
		for($i = 0; $i < count($dataArr); $i++)
		{

			$color = (imagecolorallocate($image, $colors[$i][0], $colors[$i][1], $colors[$i][2]));

			$endPoint = $startPoint + 360 * $dataArr[$i];  

			imagefilledarc($image, $width/2, $height/2, $width, $height, $startPoint, $endPoint, $color, IMG_ARC_PIE);

			$startPoint = $endPoint;
		}

		echo $image;
	}
}