<?php
/**
 * This file implements the class Exif.
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
 * Exif
 *
 * Reads the exif of an image and outputs it in a
 * nice, readable, html format.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class Exif implements HTMLObject
{
	/// Conversion array for exif values
	private $wanted=array();
	
	/// Exif values, nice and clean
	private $exif=array();
	
	/**
	 * Create Exif class
	 *
	 * @param string $file 
	 * @author Thibaud Rohmer
	 */
	public function __construct($file=null){
		/// No file given
		if(!isset($file)) return;
		
		/// File isn't an image
		if(File::Type($file) != "Image")
			throw new Exception("$file is not an image");
			
		/// File type isn't readable with exif_read_data()
		if(File::Type($file) == "png"){
			$infos['']="Impossible to display Exif.";
			return;
		}	
		
		/// No exif extension installed
		if (!in_array("exif", get_loaded_extensions())) {
			$infos['']="Exif extension is not installed on the server available";
			return;
		}
		
		/// No right to view
		if(!Judge::view($file))
			return;

		/// Create wanted table
		$this->init_wanted();
		
		/// Read exif
		$raw_exif	=	@exif_read_data($file);
		
		/// Parse exif
		foreach($this->wanted as $name => $data){
			foreach($data as $d){
				if(isset($raw_exif[$d])){
					$this->exif[$name]	=	$this->parse_exif($d,$raw_exif);
				}	
			}
		}	
	}
	
	/**
	 * Create wanted array
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function init_wanted(){
		$this->wanted['Nom'][]			=	'FileName';
		$this->wanted['APN'][]		=	'Model';
		//$this->wanted['Make'][]			=	'Make';
		$this->wanted['Expo'][]			=	'ExposureTime';
		$this->wanted['Long. focale'][]	=	'FocalLength';
		$this->wanted['Ouverture'][]		=	'FNumber';
		$this->wanted['ISO'][]			=	'ISOSpeedRatings';
	}
	
	/**
	 * Display Exif on website
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		echo "<table>";
		foreach($this->exif as $name=>$value){
			echo "<tr><td class='td_data'>$name</td><td class='td_value'>$value</td></tr>\n";
		}
		echo "</table>\n";
	}
	
	/**
	 * Parse a string referencing a fraction,
	 * and returns the value of the function
	 *
	 * @param string $f 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	function frac2float($f){
		$frac	=	explode('/',$f);
		$float	=	$frac[0]/$frac[1];
		return $float;
	}
	
	/**
	 * Parse exif data
	 *
	 * @param string $d 
	 * @param string $raw_exif 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function parse_exif($d,$raw_exif){
		
		/// Values that don't need to be processed
		$untouched=array('FileName','Model','Make','ISOSpeedRatings');
		
		/// If value doesn't need to be processed, return it
		if(in_array($d,$untouched)) 
			return $raw_exif[$d];
		
		/// Return value
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
