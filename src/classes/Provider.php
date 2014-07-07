<?php /**
 * This file implements the class Provider.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * This file is part of PhotoShow.
 *
 * PhotoShow is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhotoShow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Provider
 *
 * The provider, as its name suggests, provides stuff. It
 * is this object that looks on the disk and outputs requested
 * file, if the user is allowed to see it. All of the output is
 * done as HTML.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Provider
{
	/**
	 * Get image orientation from exif
	 */
	public static function get_orientation_degrees ($filename)
	{
		if (in_array("exif", get_loaded_extensions()))
		{
			$raw_exif = @exif_read_data ($filename);
			switch ($raw_exif['Orientation'])
			{
				case 1:
				case 2:
					$degrees = 0; 
					break;
				case 3:
				case 4:
					$degrees = 180; 
					break;
				case 5:
				case 6: 
					$degrees = -90; 
					break;
				case 7:
				case 8: 
					$degrees = 90; 
					break;
				default: 
					$degrees = 0;
			}
		}else{
			$degrees = 0;
		}

		return $degrees;
	}


	/**
	 * Autorotate image
	 */
	private static function autorotate_jpeg ($filename)
	{
		$raw_image = imagecreatefromjpeg($filename);
		$degrees = Provider::get_orientation_degrees ($filename);
		if($degrees > 0){
			$rotated_image = imagerotate($raw_image, $degrees, 0);
			if($rotated_image == NULL){
				return $raw_image;
			}
		}else{
			$rotated_image = $raw_image;
		}

		return $rotated_image;
	}

	/**
	 * Provide a video  to the user, if he is allowed to
	 * see it. 
	 *
	 * @param string $file 
	 * @return void
	 * @author Franck Royer
	 */
    public static function Video($file){
        //error_log('DEBUG/Provider::video: '.$file);

        if( !Judge::view($file)){
            return;
        }

        /// Check item
        $file_type = File::Type($file);

        if (!$file_type == "Video"){
            error_log('ERROR/Provider.php: Vid called on a non-video file '.$file);
            return;
        }

        Video::FastEncodeVideo($file);

        $basefile	= 	new File($file);
        $basepath	=	File::a2r($file);
        $path	=	Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.".webm";	

        if(!isset($path) || !file_exists($path)){
            error_log('ERROR/Provider::Video: path:'.$path.' does not exist, using '.$file);
            $path = $file;
        }

        $expires = 60*60*24*14;
        $last_modified_time = filemtime($path); 
        $last_modified_time = 0;
        $etag = md5_file($file); 

        header("Last-Modified: " . 0 . " GMT");
        header("Pragma: public");
        header("Cache-Control: max-age=360000");
        header("Etag: $etag"); 
        header("Cache-Control: maxage=".$expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
        header('Content-type: video/webm');
        readfile($path);
    }

	public static function thumb($file){
        require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';

        $path = File::r2a(File::a2r($file),Settings::$thumbs_dir);

        // We check that the thumb already exists, was created after the image, at the right size
        $goodThumb = false;
        if(file_exists($path) && filectime($file) < filectime($path) ){
        	$dim = getimagesize($path);
        	$goodThumb = ($dim[0] == Settings::$thumbs_size && $dim[1] == Settings::$thumbs_size );
        }

        if( !$goodThumb ){
        	/// If we need to create a thumb, then this is a new picture

        	if(Judge::is_public($file)){
	        	$r = new RSS(Settings::$conf_dir."/photos_feed.txt");
	        	$webpath = Settings::$site_address."?f=".urlencode(File::a2r($file));
	        	$r->add(basename($file),$webpath, "<img src='$webpath&t=Thb' />");
	        }

            /// Create directories
            if(!file_exists(dirname($path))){
                @mkdir(dirname($path),0750,true);
            }

            /// Create thumbnail
			$thumb = PhpThumbFactory::create($file);
			$thumb->adaptiveResize(Settings::$thumbs_size, Settings::$thumbs_size);

			if(File::Type($file)=="Image"){
				$thumb->rotateImageNDegrees(Provider::get_orientation_degrees ($file));	
			}
			$thumb->save($path);
			chmod($path,0775);
		}

		/* Implementation of webp... for later.
		$webp = $path.".webp";
		if(!file_exists($webp) ||  filectime($webp) < filectime($path) ){
			imagewebp(imagecreatefromjpeg($path),$webp);
		}
		*/

		return $path;
	}

	public static function small($file){
		require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';

		$basefile	= 	new File($file);
		$basepath	=	File::a2r($file);
		$webimg	=	dirname($basepath)."/".$basefile->name."_small.".$basefile->extension;
		
		list($x,$y) = getimagesize($file);
		if($x <= 1200 && $y <= 1200){
			return $file;
		}
		
		$path =	File::r2a($webimg,Settings::$thumbs_dir);

		if(!file_exists($path) || filectime($file) > filectime($path)  ){
			/// Create smaller image
			if(!file_exists(dirname($path))){
				@mkdir(dirname($path),0755,true);
			}
			$options = array('resizeUp' => true, 'jpegQuality' => 70);
			$thumb = PhpThumbFactory::create($file,$options);
			$thumb->resize(1200, 1200);
			if(File::Type($file)=="Image"){
				$thumb->rotateImageNDegrees(Provider::get_orientation_degrees($file));	
			}
			$thumb->save($path);
		}
		return $path;
	}

	/**
	 * Provide an image to the user, if he is allowed to
	 * see it. If $thumb is true, provide the thumb associated
	 * to the image.
	 *
	 * @param string $file 
	 * @param string $thumb 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function Image($file,$thumb=false,$large=false,$output=true,$dl=false){
		
		if( !Judge::view($file)){
			return;
		}
		if(function_exists("error_reporting")){
			error_reporting(0);
		}

        /// Check item
        $file_type = File::Type($file);

        switch($file_type){
        	case "Image":	$is_video = false;
        					break;
        	case "Video":	$is_video = true;
        					break;
        	default:		return;
        }

        //error_log('DEBUG/Provider::image: '.$file.' '.($is_video?'is_video':''));

        if(!$large){
            try {
                if ($is_video){
                    //TODO: fix so when opening the folder the first time no need to do F5 to see
                    //the freshly created thumbnail
                    Video::FastEncodeVideo($file);
                    $basefile	= 	new File($file);
                    $basepath	=	File::a2r($file);
                    $path =	Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.".jpg";	
                }elseif($thumb){ // Img called on a video, return the thumbnail
                    $path = Provider::thumb($file);
                }else{
                    $path = Provider::small($file);
                }
            }catch(Exception $e){
                // do nothing
            }
        }

        if(!isset($path) || !file_exists($path)){
            error_log('ERROR/Provider::image path:'.$path.' does not exist, using '.$file);
            $path = $file;
        }

        if($output){
			if($dl){
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
			}else{
				$expires = 60*60*24*14;
				$last_modified_time = filemtime($path); 
				$last_modified_time = 0;
				$etag = md5_file($file); 

		    	header("Last-Modified: " . 0 . " GMT");
				header("Pragma: public");
				header("Cache-Control: max-age=360000");
				header("Etag: $etag"); 
				header("Cache-Control: maxage=".$expires);
				header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
			}

	        header('Content-type: image/jpeg');
            if(File::Type($path)=="Image"){
            	readfile($path);
            	return;
                try {
                    imagejpeg(Provider::autorotate_jpeg ($path));	
                }catch(Exception $e){
                    error_log('ERROR/Provider.php: cannot rotate '.$path.': '.$e);
                    readfile($path);
                }
            }else{
                readfile($path);
            }
        }
    }

	/**
	 * Generates a zip.
	 *
	 * @param string $dir  
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function Zip($dir){

		/// Check that user is allowed to acces this content
		if( !Judge::view($dir)){
			return;
		}	


                // Get the relative path of the files
		$delimPosition = strrpos($dir, '/');
		if (strlen($dir) == $delimPosition) {
		        echo "Error: Directory has a slash at the end";
		        return;
		}

		// Create list with all filenames
		$items = Menu::list_files($dir,true);
		$itemsString = '';

		foreach($items as $item){
			if(Judge::view($item)){
                                // Use only the relative path of the filename
				$item = str_replace('//', '/', $item);
				$itemsString.=" '".substr($item,$delimPosition+1)."'";
			}
		}

		// Close and send to user
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename=\"".htmlentities(basename($dir), ENT_QUOTES ,'UTF-8').".zip\"");

                // Store the current working directory and change to the albums directory
		$cwd = getcwd();
		chdir(substr($dir,0,$delimPosition));

		// ZIP-on-the-fly method copied from http://stackoverflow.com/questions/4357073
		//
		// use popen to execute a unix command pipeline
		// and grab the stdout as a php stream
		$fp = popen('zip -n .jpg:.JPG:.jpeg:.JPEG -0 - ' . $itemsString, 'r');

		// pick a bufsize that makes you happy (8192 has been suggested).
		$bufsize = 8192;
		$buff = '';
		while( !feof($fp) ) {
		        $buff = fread($fp, $bufsize);
                        echo $buff;
                        /// flush();
                }
                pclose($fp);
                
                // Chang to the previous working directory
                chdir($cwd);
	}

}

?>
