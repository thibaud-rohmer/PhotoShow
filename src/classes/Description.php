<?php
/**
 * This file implements the class Description.
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
 * Description
 *
 * Implements the creating, reading, editing, and
 * displaying of the description, from and to an xml
 * file.
 * The file is stored in
 * [Thumbs]/[imagepath]/.[image]_description.xml
 * Description Structure:
 * 	- Login
 * 	- Date
 * 	- Content
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class Description implements HTMLObject
{
	/// Array of the description infos
	private $description=array();

	/// Path to item
	private $file;

	/// Path to description file
	private $descriptionfile;

	/// Urlencoded version of relative path to item
	private $webfile;

	/**
	 * Read description for item $file
	 *
	 * @param string $file
	 */
	public function __construct($file=null){

		/// No item, no description !
		if(!isset($file) || is_array($file)) return;

		/// No right to view
		if(!Judge::view($file))
			return;

		/// Set variables
		$this->file	=	$file;
		$basepath	=	File::a2r($file);

		/// Urlencode basepath
		$this->webfile = urlencode(File::a2r($file));

		/// Build relative path to description file
		if(is_file($file)){
			$description	=	dirname($basepath)."/.".mb_basename($file)."_description.xml";
		}else{
			$description	=	$basepath."/.description.xml";
		}

		/// Set absolute path to description file
		$this->descriptionfile =	File::r2a($description,Settings::$thumbs_dir);

		/// Check that description file exists
		if(file_exists($this->descriptionfile)){
			$this->parse_description_file();
		}
	}

	/**
	 * Add a description for item $file
	 *
	 * @param string $file
	 * @param string $login
	 * @param string $description
	 */
	public static function add($file,$content,$login=""){

		/// Just to be really sure...
		if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
			return;
		}

		/// Get context
		$description = new Description($file);

		if(empty($content)){
			/// Description is empty, might as well delete the file
			if(file_exists($description->descriptionfile))
				unlink($description->descriptionfile);
			return;
		}

		/// Store the description
		$description->description['login'] = $login;
		$description->description['content'] = $content;
		$description->description['date'] = date('j-m-y, h-i-s');

		/// And save it
		$description->save();
	}


	public function save(){

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><description></description>');

		$xml->addChild("login"	, $this->description['login']);
		$xml->addChild("date"	, $this->description['date']);
		$xml->addChild("content", $this->description['content']);

		if(!file_exists(dirname($this->descriptionfile))){
			@mkdir(dirname($this->descriptionfile),0750,true);
		}
		/// Write xml
		$xml->asXML($this->descriptionfile);
	}


	/**
	 * Read content of description file, and
	 * store it in variables
	 *
	 * @return void
	 */
	private function parse_description_file(){
		$xml		=	simplexml_load_file($this->descriptionfile);
		$this->description['login'] = $xml->login;
		$this->description['date'] = $xml->date;
		$this->description['content'] = $xml->content;
	}


	/**
	 * Display description on website
	 *
	 * @return void
	 */
	public function toHTML($forInfosMenu=false){
		if(!$this->file)
			return;

		$desc = stripslashes(htmlentities($this->description['content'], ENT_QUOTES ,'UTF-8'));

		if (!$forInfosMenu)
			echo nl2br($desc);
		else if(CurrentUser::$admin || CurrentUser::$uploader){
			// It's for the Infos menu, but only the admin or an uploader can write a description
			echo '<h3>'.Settings::_("description","description").'</h3>';

			echo "<form action='?t=Des&f=".$this->webfile."' class='pure-form pure-form-stacked' id='description_form' method='post'><fieldset class='transparent'>\n";
			echo "<input type='hidden' name='login' id='login' value='".htmlentities(CurrentUser::$account->login, ENT_QUOTES ,'UTF-8')."' readonly>";
			echo "<textarea name='content' id='content' placeholder='".Settings::_("description","description")."'>".$desc."</textarea>\n";
			echo "<input type='submit' class='pure-button pure-button-primary' value='".Settings::_("description","submit")."'></fieldset>\n";
			echo "</form>\n";
		}

	}
}

?>