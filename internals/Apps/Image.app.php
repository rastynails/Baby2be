<?php

class app_Image
{
	private static $gd_version;
	
	private static $SUPPORTED_IMAGE_TYPES = array( IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG );
	
	const ERROR_UPLOAD_EMPTY_FILE_UNIQID = 1;
	const ERROR_UPLOAD_EMPTY_RESULT_PATH = 2;
	
	const ERROR_CONVERT_EMPTY_PATH = 3;
	
	const ERROR_RESIZE_EMPTY_PATH = 4;
	
	const ERROR_MAX_RESOLUTION = 5;
	const ERROR_MAX_FILESIZE = 6;
	
	const ERROR_WRONG_IMAGE_TYPE = 7;
	const ERROR_GD_LIB_NOT_INSTALLED = 8;
	
	
	const MEMORY_LIMIT = '32M';
			
	
	public static function construct()
	{
		self::$gd_version = self::getGdVersion();
	}
	
	public static function getMemoryLimit()
	{
		$server_mem_limit = intval(ini_get('memory_limit'));
		if ($server_mem_limit < intval(self::MEMORY_LIMIT)) {
			return self::MEMORY_LIMIT;
		}
		
		return $server_mem_limit;
	}
	
	public static function prepareMemoryLimit() {
		$server_mem_limit = intval(ini_get('memory_limit'));
		if ($server_mem_limit < intval(self::MEMORY_LIMIT)) {
			ini_set('memory_limit', self::MEMORY_LIMIT);
		}
	}
	
	
	public static function getGdVersion()
	{
		$gd_info = gd_info();
		if ( !$gd_info ){
			return false;
		}   	
		 
		preg_match( '/(\d)\.(\d)/', $gd_info['GD Version'], $match );
			   
		return $match[1];
	}
	
	/**
	 * Uploads Image file to server
	 *
	 * @param string file_uniqid
	 * @param string result_path
	 * @param int result_image_type
	 * @param int result_width
	 * @param int result_height
	 * @return bool
	 */
	public static function upload(array $options)
	{
		if (!isset($options['file_uniqid'])) {
			throw new SK_ImageException('empty_file_uniqid', self::ERROR_UPLOAD_EMPTY_FILE_UNIQID );
		}
		
		if (!isset($options['result_path'])) {
			throw new SK_ImageException('empty_result_path', self::ERROR_UPLOAD_EMPTY_RESULT_PATH );
		}
			
		$file_uniqid = $options['file_uniqid'];
		$result_path = $options['result_path'];	
		$type = (int) $options["result_image_type"];
		
		$tmp_file = new SK_TemporaryFile($file_uniqid);
		list($iwidth, $iheight, $t) = getimagesize($tmp_file->getPath());
		$width = empty($options["result_width"]) ? $iwidth : (int)$options["result_width"];
        $height = empty($options["result_height"]) ? $iheight : (int)$options["result_height"];
        
		$config = SK_Config::section("photo")->Section("general");
		
		if ($tmp_file->getSize() > 1024 *  1024 * $config->max_filesize) {
			throw new SK_ImageException('more_than_max_filesize', self::ERROR_MAX_FILESIZE  );
		}
		
        if ($width > $config->max_width || $height > $config->max_height) {
            throw new SK_ImageException("more_then_max_resolution", self::ERROR_MAX_RESOLUTION);
        }
		
		$result = self::convert($tmp_file->getPath(), $type, $result_path);
		if ($result && ($width && $height) ) {
			return self::resize($tmp_file->getPath(), $width, $height, $result_path);
		}
		return $result;
	}
	
	
	public static function resource2image($resource, $path, $type = IMAGETYPE_JPEG)
	{
		self::prepareMemoryLimit();
		
		$result = false;
		switch ( $type )
		{
			case IMAGETYPE_JPEG:
				$result = imagejpeg( $resource, $path , UPLOAD_IMG_QUALITY );	
				break;
			case IMAGETYPE_GIF:
				$result = imagegif( $resource, $path );
				break;
			case IMAGETYPE_PNG:
				$result = imagepng( $resource, $path );
				break;
		}
		imagedestroy($resource);
		return $result;
	}
	
	public static function image2recource($path, $type)
	{
		self::prepareMemoryLimit();
		
		$src_resource = null;
		switch ( $type )
		{
			case 1:
				$src_resource = Imagecreatefromgif( $path );
				break;
			case 2:
				$src_resource = ImagecreatefromJpeg ( $path );
				break;
			case 3:
				$src_resource = Imagecreatefrompng ( $path );
				break;
			default: 
				throw new SK_ImageException('wrong_original_type', self::ERROR_WRONG_IMAGE_TYPE );
				break;
		}
		
		return $src_resource;
	}
	
	public static function convert($path, $type = IMAGETYPE_JPEG, $result_path = null)
	{
			
		if (!self::$gd_version) {
			throw new SK_ImageException('gd_lib_not_installed', self::ERROR_GD_LIB_NOT_INSTALLED );
		}
		
		if ( !($path = trim($path)) ) {
			throw new SK_ImageException('empty_image_path', self::ERROR_CONVERT_EMPTY_PATH );			
		}
		
		$type = in_array($type, self::$SUPPORTED_IMAGE_TYPES ) ? $type : IMAGETYPE_JPEG;
		
				
		list($width, $height, $original_type) = getimagesize($path);
		
		if ($original_type == $type) {
			if (isset($result_path)) {
				copy($path, $result_path);
			}
			return true;
		}
		
		$resource = self::image2recource($path, $original_type);
			
		$result_path = isset($result_path) ? $result_path : $path;
		$result = false;
		
		return self::resource2image($resource, $result_path, $type);
		
	}
	
	
	public static function resize( $source_path, $width, $height, $square = false, $result_path = null, $crop = null)
	{
		
		if (!self::$gd_version) {
			throw new SK_ImageException('gd_lib_not_installed', self::ERROR_GD_LIB_NOT_INSTALLED );
		}
		
		if ( !($source_path = trim($source_path)) ) {
			throw new SK_ImageException('empty_image_path', self::ERROR_RESIZE_EMPTY_PATH );			
		}
		
		$result_path = isset($result_path) ? trim($result_path) : $source_path;
		
		list($source_width, $source_height, $source_type) = getimagesize($source_path);
		
		if ( ($source_width == $width) && ($source_height == $height) ) {
			if (isset($result_path)) {
				copy($source_path, $result_path);
			}
			return true;	
		}
						
		$resource = self::image2recource($source_path, $source_type);
		
		$config = SK_Config::section("photo")->Section("general");
				
		$crop = isset($crop) ? (bool) $crop : $config->crop_thumb;
		
		if ($square) {
			self::makeSquare($resource, $source_width, $source_height, $crop, $config->thumb_fill_color);
		}
		
		if ( ( ( $source_width > $width ) && $width ) || ( ( $source_height > $height ) && $height ) )
		{
			if ( $source_height/$height > $source_width/$width ) {
				$scale_coef = $height/$source_height;
			}
			else { 
				$scale_coef = $width/$source_width;
			}
		
			$dest_height = $source_height*$scale_coef;
			$dest_width = $source_width*$scale_coef;
		}
		else
		{
			$dest_height = $source_height;
			$dest_width = $source_width;
		}
		
		$result = false;
		
		if ( self::$gd_version >= 2 )
		{
			$dist_resource = imagecreatetruecolor( $dest_width, $dest_height );
			$result = imagecopyresampled( $dist_resource, $resource, 0, 0, 0, 0, $dest_width, $dest_height, $source_width, $source_height);		
		}
		else 
		{
			$dist_resource = imageCreate( $dest_width, $dest_height );
			$result = imagecopyresized( $dist_resource, $resource, 0, 0, 0, 0, $dest_width, $dest_height, $source_width, $source_height);
		}
		
		if (!$result) {
			return null;
		}
		imagedestroy($resource);
		return self::resource2image($dist_resource, $result_path, $source_type);
	}
	
	public static function makeSquare( & $resource, & $width, & $height, $crop = true, $hex_color = '000000')
	{
		$src_x = 0;
		$src_y = 0;
		
		if ($width == $height) {
			return $resource;
		}
				
		if ($width > $height) {
			if ($crop) {
			$src_x = ($width - $height) / 2;
			$side_size = $width - ($width - $height);
			} else {
				$src_y = ($width - $height) / 2;
				$side_size = $width;
			}
		}
		else {
			if ($crop) {
			$src_y = ($height - $width) / 2;
			$side_size = $height - ($height - $width);	
			} else {
				$src_x = ($height - $width) / 2;
				$side_size = $height;
			}
		}
		
		$result = false;
		
		if ( self::$gd_version >= 2 )
		{
			$dist_resource = imagecreatetruecolor( $side_size, $side_size );
			
			if ($crop) {
				$result = imagecopyresampled( $dist_resource, $resource, 0, 0, $src_x, $src_y, $width, $height, $width, $height);		
			} else {
				$rgb = self::hex2rgb($hex_color);
				$bg = imagecolorallocate($dist_resource, $rgb['r'], $rgb['g'], $rgb['b']);
				imagefill($dist_resource, 1, 1, $bg);
				$result = imagecopyresampled( $dist_resource, $resource, $src_x, $src_y, 0, 0, $width, $height, $width, $height);
			}
		}
		else 
		{
			$dist_resource = imageCreate( $side_size, $side_size );
			if ($crop) {
			$result = imagecopyresized( $dist_resource, $resource, 0, 0, $src_x, $src_y, $width, $height, $width, $height);
			} else {
				$result = imagecopyresized( $dist_resource, $resource, $src_x, $src_y, 0, 0, $width, $height, $width, $height);
			}
		}
		imagedestroy($resource);
		
		$resource = $dist_resource;
		$width = $height = $side_size;
		return $result;

	}
	
	public static function hex2rgb($hexstr, $rgb = null) {
		$str_length = strlen($hexstr);
		if ($str_length==3) {
			$_hexstr = $hexstr;
			$hexstr = '';
			for ($i=0; $i<3; $i++) {
				$hexstr .= $_hexstr{$i} .$_hexstr{$i};
			}
		}
		$int = hexdec($hexstr);
	 	switch($rgb) {
	    	case "r":
	        	return 0xFF & $int >> 0x10;
	        case "g":
		        return 0xFF & ($int >> 0x8);
	        case "b":
	    	    return 0xFF & $int;
	        default:
	        	return array(
		            "r" => 0xFF & $int >> 0x10,
		            "g" => 0xFF & ($int >> 0x8),
		            "b" => 0xFF & $int
		            );
	    }   
	}
	
	/**
	 * Crops Image Resource
	 *
	 * @param string source_path
	 * @param string dist_path
	 * @param int left
	 * @param int top
	 * @param int right
	 * @param int bottom
	 * @return bool
	 */
	public static function crop(array $params)
	{
		$left = isset($params['left']) ? intval($params['left']) : 0;
		$top = isset($params['top']) ? intval($params['top']) : 0;
		$right = isset($params['right']) ? intval($params['right']) : 0;
		$bottom = isset($params['bottom']) ? intval($params['bottom']) : 0;
		
		
		$source = $params['source_path'];
		$dist = isset($params['dist_path']) ? $params['dist_path'] : $source; 
		
		list($width, $height, $type) = getimagesize($source);
		
		$source_resource = self::image2recource($source, $type);
		
		$width_dist = $width - $right - $left;
		$height_dist = $height - $bottom - $top;
			
		if ( self::$gd_version >= 2 )
		{
			$dist_resource = imagecreatetruecolor( $width_dist, $height_dist );
			$result = imagecopyresampled( $dist_resource, $source_resource, 0, 0, $left, $top, $width, $height, $width, $height);		
		}
		else 
		{
			$dist_resource = imageCreate( $width_dist, $height_dist );
			$result = imagecopyresized( $dist_resource, $source_resource, 0, 0, $left, $top, $width, $height, $width, $height);
		}
		
		return self::resource2image($dist_resource, $dist, $type);
	}
	
	public static function rotate($path, $angle, $result_path = null) 
	{
        if (!self::$gd_version) {
            throw new SK_ImageException('gd_lib_not_installed', self::ERROR_GD_LIB_NOT_INSTALLED );
        }
        
        if ( !($path = trim($path)) ) {
            throw new SK_ImageException('empty_image_path', self::ERROR_CONVERT_EMPTY_PATH );           
        }
        
        list($width, $height, $type) = getimagesize($path);
        $resource = self::image2recource($path, $type);
        
        $rotated = self::imageRotate($resource, $angle);
        
        return (bool) self::resource2image($rotated, empty($result_path) ? $path : $result_path);
	}
	
	private static function imageRotate($resource, $angle)
	{
	    /*if ( function_exists('imagerotate') )
	    {
	        return imagerotate($resource, $angle, 0);
	    }*/
	    
	    return self::_imageRotate($resource, $angle);
	}
	
	private static function _imageRotate($img, $rotation)
	{
        $width = imagesx($img);
        $height = imagesy($img);
        
        switch($rotation) 
        {
            case 90:
            case 180: 
            case 270: 
                $newimg= @imagecreatetruecolor($height , $width );
                break;
            case 0: 
                return $img;
            case 360: 
                return $img;
        }

        if($newimg) 
        {
            for($i = 0;$i < $width ; $i++) 
            {
                for($j = 0;$j < $height ; $j++) 
                {
                    $reference = imagecolorat($img,$i,$j);
                    switch($rotation) 
                    {
                        case 90: 
                            if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference ))
                            {
                              return false;
                            }
                            break;
                        case 180: 
                            if(!@imagesetpixel($newimg, $i, ($height - 1) - $j, $reference ))
                            {
                              return false;
                            }
                            break;
                        case 270: 
                            if(!@imagesetpixel($newimg, $j, $width - $i, $reference ))
                            {
                              return false;
                            }
                            break;
                    }
                }
            }
            
            return $newimg;
        }
        
        return false;
    }
}



class SK_ImageException extends Exception 
{
	private $error_key;
	
	/**
	 * Constructor.
	 *
	 * @param string $error_key
	 */
	public function __construct( $error_key , $code = null)
	{
		$this->error_key = $error_key;
		
		parent::__construct('', $code);
	}
	
	/**
	 * Get Image error key.
	 *
	 * @return string
	 */
	public function getErrorKey()
	{
		return $this->error_key;
	}
}


app_Image::construct();
