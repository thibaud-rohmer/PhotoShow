<?php
/*
    This file is part of PhotoShow.

    PhotoShow is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhotoShow is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
*/

class File
{
	public $path;
	public $extension;
	public $name;
	public $type;
	
	/**
	 * Check that file exists, and parse its infos (extension,name,type)
	 *
	 * @param string $path 
	 * @author Thibaud Rohmer
	 */
	public function __construct($path){
		if(!file_exists($path))
			throw new Exception("The file $path doesn't exist");
		$this->path			=	$path;
		$this->extension	=	self::Extension($path);
		$this->name			=	self::Name($path);	
		$this->type			=	self::Type($path);
	}
	
	/**
	 * Return the extension of $file
	 *
	 * @param string $file 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function Extension($file){
		$info = pathinfo($file);
		return $info['extension'];
	}
	
	/**
	 * Return the name of $file, without the extension
	 *
	 * @param string $file 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function Name($file){
		$info	=	pathinfo($file);
		return	basename($file,'.'.$info['extension']);
	}
	
	/**
	 * Return the type of $file
	 *
	 * @param string $file 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function Type($file){
		$ext	=	self::Extension($file);
		if(!isset($ext)){
			return "folder";
		}
		$types	=	array();
		
		$types['Image'][]	=	"png";
		$types['Image'][]	=	"jpg";
		$types['Image'][]	=	"jpeg";
		$types['Image'][]	=	"tiff";
		$types['Image'][]	=	"gif";
		
		$types['Video'][]	=	"flv";
		$types['Video'][]	=	"mov";
		$types['Video'][]	=	"mpg";
		
		
		$types['File'][]	=	"xml";
		
		foreach($types as $type=>$typetab){
			if(in_array($ext,$typetab))
				return $type;
		}

		throw new Exception("Unsupported Type for $file");

	}
	
	/**
	 * Absolute path comes in, relative path goes out !
	 *
	 * @param string $file 
	 * @param string $dir Directory from where the relative path will be (if NULL : photos_dir)
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function a2r($file,$dir=NULL){
		if(!isset($dir)){
			$dir		=	Settings::$photos_dir;
		}
		
		$rf	=	realpath($file);
		$rd =	realpath($dir);
		
		
		if( substr($rf,0,strlen($rd)) != $rd ){
			throw new Exception("$file is not inside $dir<br/> $rf<br/>$rd");
		}

		return ( substr($rf,strlen($rd) + 1 ) );
	}

	/**
	 * Relative path comes in, absolute path goes out !
	 *
	 * @param string $file 
	 * @param string $dir 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function r2a($file,$dir=NULL){
		if(!isset($dir)){
			$dir		=	Settings::$photos_dir;
		}
		
		return $dir."/".$file;
	}
	
	/**
	 * Path comes in, relative and absolute path come out
	 *
	 * @param string $path 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function paths($path,$dir=NULL){
		if(!isset($dir)){
			$settings	=	new Settings();
			$dir		=	$settings->photos_dir;
		}
		try{
			$rel		=	File::a2r($path,$dir);
			$abs		=	$path;
		}catch(Exception $e){
			// This path is already relative
			$rel		=	$path;
			$abs		=	File::r2a($path,$dir);
		}
		
		return array($rel,$abs);
	}
}
?>