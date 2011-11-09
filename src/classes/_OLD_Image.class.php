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

class Image
{
	public $relative;
	public $absolute;
	public $thumb;
	
	public function __construct($relative){
		$settings = new Settings();
		
		$this->$relative	=	$path;
		$this->$absolute	=	$settings->photos_dir."/".$relative;
		$this->$thumb		=	$settings->thumbs_dir."/".$relative;
	}
	
	public function create_thumb(){
		// TODO : Change this
		if(!file_exists(dirname($this->thumb))){
			mkdir(dirname($dest),0750,true);
		}
		
		$thumb = PhpThumbFactory::create($this->absolute);
		$thumb->resize(200, 200);
		$thumb->save($this->thumb);
	}
	
	public function dimensions(){
		list($x,$y)	= getimagesize($this->$relative);
		return array($x,$y);
	}
	
	public static function ratio($image){
		list($x,$y) = getimagesize($image);
		$ratio	=	floor($x/$y)+1;
		if($ratio>3) $ratio=3;
		return $ratio;
	}
	
	public function exif(){
		try{
			$data=exif_read_data($this->absolute);
			return new Exif($data);
		}catch(Exception $e){
			// Cannot read data
		}
	}
}