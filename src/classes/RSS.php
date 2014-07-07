<?php
/**
 * This file implements the class Guest Token
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	  PhotoShow
 * @category  Website
 * @author	  Franck Royer <thibaud.rohmer@gmail.com>
 * @copyright 2012 Franck Royer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * RSS
 *
 * Implements functions to work with a Guest Token (or key)
 * Read the account from the Guest Token XML file,
 * edit it, and save it.
 * 
 * 
 * @package	  PhotoShow
 * @category  Website
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */
class RSS
{

	private $feed = "";

    public function __construct($feed){
    	$this->feed = $feed;
    }


	public function item($n,$p,$d){
		return "<item><title>$n</title><link>$p</link><description><![CDATA[ $d ]]></description></item>\n"; 
	}

	public function add($name,$path,$desc){
		$item = $this->item($name,$path,$desc);
		$current = file_get_contents($this->feed);
		file_put_contents($this->feed,$item.$current, LOCK_EX);
	}

	public function clean(){
		// read the file in an array.
		$file = file($this->feed);

		// slice first 20 elements.
		$file = array_slice($file,0,20);

		// write back to file after joining.
		file_put_contents($this->feed,implode("",$file));
	}

	public function toXML(){
		$this->clean();
		if(Settings::$rss);
		header("Content-type: text/xml"); 
		echo "<?xml version='1.0' encoding='UTF-8'?> 
<rss version='2.0'>
<channel>
<title>".Settings::$name."</title>
<link>".Settings::$site_address."</link>
<description>Photos Feed</description>
<language>en-us</language>"; 
		readfile($this->feed);
		echo "</channel></rss>";
	}
}