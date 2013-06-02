<?php

class MySize
{
	var $Width;
	var $Heigth;

	function MySize($w, $h)
	{
		$this->Width = $w;
		$this->Height = $h;
	}
}

class ROP
{

    function MakeThumbnail($srcBmpFilename, $faceRectX, $faceRectY, $faceRectWidth, $faceRectHeight, $prefWidth, $prefHeight)
    {
		$ROPMaxThumbEdgeSize = 400;
		$ROPMaxWidthHeightAspectRatio = 6.0 / 3.0;
		
        if ($prefWidth > 0 
            && $prefHeight > 0 
            && $prefWidth <= $ROPMaxThumbEdgeSize 
            && $prefHeight <= $ROPMaxThumbEdgeSize 
            && ((double)$prefWidth / (double)$prefHeight) < $ROPMaxWidthHeightAspectRatio
            && ((double)$prefHeight / (double)$prefWidth) < $ROPMaxWidthHeightAspectRatio)
        {
        	list ($srcBmpWidth, $srcBmpHeight, $srcBmpType) = getimagesize( $srcBmpFilename );
			if ( !in_array( $srcBmpType, array( 1,2,3 ) ) )
			{
				return false;
			}
        	
            $Divisor = 3;
            $w = ROP::GetArrayMinimum( array($faceRectWidth / $Divisor, $faceRectX, $srcBmpWidth - $faceRectX - $faceRectWidth) );
            $h = ROP::GetArrayMinimum( array($faceRectHeight / $Divisor, $faceRectY, $srcBmpHeight - $faceRectY - $faceRectHeight) );
            $width = $faceRectWidth + 2 * $w;
            $height = $faceRectHeight + 2 * $h;
            $x = $faceRectX - $w;
            $y = $faceRectY - $h;

            $prefWHAspectRatio = (double)$prefWidth / (double)$prefHeight;
            $realWHAspectRatio = ( $height ) ? (double)$width / (double)$height : 0;

            if ($prefWHAspectRatio > $realWHAspectRatio)
            {
                $tmpWidth = $height * $prefWHAspectRatio;
                $w1 = ROP::GetArrayMinimum( array(($tmpWidth - $width) / 2, $x, $srcBmpWidth - $x - $width) );
                $width = $width + 2 * $w1;
                $x = $x - $w1;
            }
            else if ($prefWHAspectRatio < $realWHAspectRatio)
            {
                $tmpHeight = $width / $prefWHAspectRatio;
                $h1 = ROP::GetArrayMinimum( array(($tmpHeight - $height) / 2, $y, $srcBmpHeight - $y - $height) );
                $height = $height + 2 * $h1;
                $y = $y - $h1;
            }
			switch ( $srcBmpType )
			{
				case 1:
					$sourceBmp = Imagecreatefromgif( $srcBmpFilename );
					break;
				case 2:
					$sourceBmp = ImagecreatefromJpeg ($srcBmpFilename );
					break;
				case 3:
					$sourceBmp = Imagecreatefrompng ($srcBmpFilename );
					break;
			}
		
			$gdVersion = ROP::GetGdVersion();

			$appropriateSize = ROP::GetAppropriateSize(new MySize($width, $height), new MySize($prefWidth, $prefHeight));
			
			if ( $gdVersion >= 2 )
			{
				$thumb = imagecreatetruecolor( $appropriateSize->Width, $appropriateSize->Height );
				imagecopyresampled( $thumb, $sourceBmp, 0, 0, $x, $y, $appropriateSize->Width, $appropriateSize->Height, $width, $height);
				
			}
			else 
			{
				$thumb = imageCreate( $appropriateSize->Width, $appropriateSize->Height );
				imagecopyresized( $thumb, $sourceBmp, 0, 0, $x, $y, $appropriateSize->Width, $appropriateSize->Height, $width, $height);
			}
            
			return $thumb;
        }
        return false;
    }
	         
	function GetAppropriateSize($src, $pref)
	{
		$proper = $pref;
        $ratio = $pref->Width / $src->Width;
        if ($ratio * $src->Height < $pref->Height)
        {
        	$proper->Height = (int)($ratio * $src->Height);
        }
        else
        {
        	$proper->Width = (int)($pref->Height / $src->Height * $src->Width);
        }
        return $proper;
	}


    /**
     * Enter description here...
     *
     * @param array of int $x
     * @return minimun element value
     */
    function GetArrayMinimum($x)
    {
        $length = count($x);
        $min = $x[0];
        for ($i = 1; $i < $length; $i++)
            if ($min > $x[$i])
                $min = $x[$i];
        return $min;
    }
    
	/**
	 * Detects GD library version.
	 * Returns number of GD library.
	 *
	 * @return integer
	 */
	function GetGdVersion()
	{
		$gdInfo = gd_info();
		if (!$gdInfo)
			return false;
			 		
			
		$match = array();
		preg_match( '/(\d)\.(\d)/', $gdInfo['GD Version'], $match );
		
		return $match[1];
	}	
}

?>