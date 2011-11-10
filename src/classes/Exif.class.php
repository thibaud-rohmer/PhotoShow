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

class Exif
{
	private $wanted=array();
	
	
	private $exif=array();
	
	
	public function __construct($file){
		if(!isset($file)) return;
		
		if(File::Type($file) != "Image")
			throw new Exception("$file is not an image");
			
		if(File::Type($file) == "png"){
			$infos['']="Impossible to display Exif.";
			return;
		}	
			
		if (!in_array(exif, get_loaded_extensions())) {
			$infos['']="Exif extension is not installed on the server available";
			return;
		}
		
		if(!CurrentUser::view($file))
			return;

		
		$this->init_wanted();
		
		$raw_exif	=	exif_read_data($file);
		
		foreach($this->wanted as $name => $data){
			foreach($data as $d){
				if(isset($raw_exif[$d])){
					$this->exif[$name]	=	$this->parse_exif($d,$raw_exif);
				}	
			}
		}	
	}
	
	private function init_wanted(){
		$this->wanted['Name'][]			=	'FileName';
		$this->wanted['Model'][]		=	'Model';
		$this->wanted['Make'][]			=	'Make';
		$this->wanted['Expo'][]			=	'ExposureTime';
		$this->wanted['Focal Length'][]	=	'FocalLength';
		$this->wanted['Aperture'][]		=	'FNumber';
		$this->wanted['ISO'][]			=	'ISOSpeedRatings';
	}
	
	
	public function toHTML(){
		echo "<div id='exif' class='box'>\n";
		echo "<div class='title'>\n";
		echo "exif";
		echo "</div>\n";
		echo "<table class='data'>";
		foreach($this->exif as $name=>$value){
			echo "<tr><td class='td_data'>$name</td><td class='td_value'>$value</td></tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";
	}
	
	function frac2float($f){
		$frac	=	explode('/',$f);
		$float	=	$frac[0]/$frac[1];
		return $float;
	}
	
	private function parse_exif($d,$raw_exif){
		// Values that don't need to be processed
		$untouched=array('FileName','Model','Make','ISOSpeedRatings');
		if(in_array($d,$untouched)) 
			return $raw_exif[$d];

		$v=0;
		switch ($d){
			case 'ExposureTime': 	$v	=	$raw_exif[$d]." s";
									break;
			case 'FocalLength':		$v		=	$this->frac2float($raw_exif[$d])." mm";
									break;
			case 'FNumber':			$v	=	$this->frac2float($raw_exif['FocalLength'])/$this->frac2float($raw_exif[$d]);
									break;
		}
		return $v;
	}
}
?>