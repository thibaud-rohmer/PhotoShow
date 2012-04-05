<?php
/**
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
		}else{
			$rotated_image = $raw_image;
		}

		return $rotated_image;
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
	public static function image($file,$thumb=false,$large=false,$output=true,$dl=false){
		
		if( !Judge::view($file)){
			return;
		}
		if(function_exists("error_reporting")){
			error_reporting(0);
		}

		/// Check item
		//~ if(!File::Type($file) || File::Type($file) != "Image"){
			//~ return;
		//~ }

		if (File::Type($file)=="Video") {
			
			$basefile	= 	new File($file);
			$basepath	=	File::a2r($file);

			/// Build relative path to webimg
			$path	=	Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.".jpg";	
			Video::FastEncodeVideo($file,$basefile->extension);
			$large = true;
		}

		if(!$large){
			try {
				if($thumb){
					$path = File::r2a(File::a2r($file),Settings::$thumbs_dir);
					if(!file_exists($path) || filectime($file) > filectime($path) ){
						require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';
						
						/// Create directories
						if(!file_exists(dirname($path))){
							@mkdir(dirname($path),0750,true);
						}
						
						/// Create thumbnail
						$thumb = PhpThumbFactory::create($file);
						$thumb->resize(200, 200);
						if(File::Type($file)=="Image"){
							$thumb->rotateImageNDegrees(Provider::get_orientation_degrees ($file));	
						}
						$thumb->save($path);
					}
				}else{
					list($x,$y) = getimagesize($file);
					if($x > 800 || $y > 600){

						require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';

						$basefile	= 	new File($file);
						$basepath	=	File::a2r($file);

						/// Build relative path to webimg
						$webimg	=	dirname($basepath)."/".$basefile->name."_small.".$basefile->extension;
						
						/// Set absolute path to comments file
						$path =	File::r2a($webimg,Settings::$thumbs_dir);

						if(!file_exists($path) || filectime($file) > filectime($path)  ){
							/// Create smaller image
							if(!file_exists(dirname($path))){
								@mkdir(dirname($path),0755,true);
							}
							$thumb = PhpThumbFactory::create($file);
							$thumb->resize(800, 800);
							if(File::Type($file)=="Image"){
								$thumb->rotateImageNDegrees(Provider::get_orientation_degrees($file));	
							}
							$thumb->resize(800, 600);
							$thumb->save($path);
						}

					}else{
						$path = $file;
					}
					
				}
			}catch(Exception $e){
				// do nothing
			}
		}
		
		if(!isset($path) || !file_exists($path)){
			$path = $file;
		}

		if($output){
			if($dl){
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Content-type: image/jpeg');
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
				header('Content-type: image/jpeg');
			}

			if(File::Type($file)=="Image"){
				imagejpeg(Provider::autorotate_jpeg ($path));	
			}else{
				readfile($path);
			}
		}
	}
	public static function Zip($dir){

		/// Check that user is allowed to acces this content
		if( !Judge::view($dir)){
			return;
		}	
			
		/// Prepare file
		$tmpfile = tempnam("tmp", "zip");
		$zip = new ZipArchive();
		$zip->open($tmpfile, ZipArchive::OVERWRITE);

		/// Staff with content
		$items = Menu::list_files($dir,true);

		foreach($items as $item){
			if(Judge::view($item)){
				$zip->addFile($item,basename(dirname($item))."/".basename($item));
			}
		}

		// Close and send to user
		$fname=basename($dir);
		$zip->close();
		header('Content-Type: application/zip');
		header('Content-Length: ' . filesize($tmpfile));
		header("Content-Disposition: attachment; filename=\"".htmlentities($fname, ENT_QUOTES ,'UTF-8').".zip\"");
		readfile($tmpfile);
		unlink($tmpfile);



	}

}

?>