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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class Provider
{


	/**
	 * Provide an image to the user, if he is allowed to
	 * see it. If $thumb is true, provide the thumb associated
	 * to the image.
	 * 
	 * TODO : if no thumb is found, generate thumb
	 *
	 * @param string $file 
	 * @param string $thumb 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function image($file,$thumb=false,$large=false){
		
		if( !Judge::view($file)){
			return;
		}
		if(function_exists(error_reporting)){
			error_reporting(0);
		}
		if(!$large){
			try {
				if($thumb){
					$path = File::r2a(File::a2r($file),Settings::$thumbs_dir);
					if(!file_exists($path)){
						require_once dirname(__FILE__).'/../phpthumb/ThumbLib.inc.php';
						
						/// Create directories
						if(!file_exists(dirname($path))){
							@mkdir(dirname($path),0750,true);
						}
						
						/// Create thumbnail
						$thumb = PhpThumbFactory::create($file);
						$thumb->resize(200, 200);
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

						if(!file_exists($path)){
							/// Create smaller image
							if(!file_exists(dirname($path))){
								@mkdir(dirname($path),0755,true);
							}
							$thumb = PhpThumbFactory::create($file);
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

		header('Content-type: image/jpeg');
		readfile($path);			
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
		header("Content-Disposition: attachment; filename=\"$fname.zip\"");
		readfile($tmpfile);
		unlink($tmpfile);



	}

}

?>