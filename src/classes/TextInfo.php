<?php
/**
 * This file implements the class TextInfo.
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
 * @author    Cédric Levasseur
 * @copyright 2011 Cédric Levasseur
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/cyr-ius/PhotoShow
 */

/**
 * TextInfo
 *
 *TextInfo contain explain text of Album
 *

 */

class TextInfo
{
	
	public $title=null;
	public $author=null;
	public $contain=null;

	/**
	 * Create a Judge for a specific file.
	 *
	 * @param string $f 
	 * @param string $read_rights 
	 * @author Thibaud Rohmer
	 */
	public function __construct($f){
		
		if(!file_exists($f)){
			return;
		}
		$this->file = $f;
		self::Get_File($f);
		self::Get_Contains();
	}
	
	/**
	 */
	private function Get_File($f){
				
		$basefile	= 	new File($f);
		$basepath	=	File::a2r($f);

		$this->filename = $basefile->name;
		$this->webpath 	= urlencode($basepath);

		if(is_file($f)){
			$textfile	=	dirname($basepath)."/.".basename($f)."_textexplain.xml";
		}else{
			$textfile	=	$basepath."/.textexplain.xml";
		}
		$this->path =	File::r2a($textfile,Settings::$thumbs_dir);

	}
	
	/**
	 */
	private function Get_Contains(){
		if (is_file($this->path))  {
			$xml			=	simplexml_load_file($this->path);
			$this->title		=	htmlspecialchars($xml->title, ENT_QUOTES ,'UTF-8');
			$this->author 	= 	htmlspecialchars($xml->author, ENT_QUOTES ,'UTF-8');
			$this->contain	= 	htmlspecialchars($xml->contain, ENT_QUOTES ,'UTF-8');
		}
	}

	/**
	 */
	public static function Save_File($f ,$title=null,$author=null,$contain=null){
		
		$ti = new TextInfo($f);
		
		/// Create xml
		$xml		=	new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><infos></infos>');
		
		/// Put values in xml
		$xml->addChild('title',$title);
		$xml->addChild('author',$author);
		$xml->addChild('contain',$contain);
		
		if(!file_exists(dirname($ti->path))){
			@mkdir(dirname($ti->path),0750,true);
		}
		/// Save xml
		$xml->asXML($ti->path);
	}
	
	public static function Delete_File($f) {
	
		/// Just to be sure, check that user is admin
		if(!CurrentUser::$admin)
			return;
	
		$ti = new TextInfo($f);
		if(file_exists($ti->path)){
			@unlink($ti->path);
		}
	
	}
	
	
	public static function Edit_File($f) {

		/// Just to be sure, check that user is admin
		if(!CurrentUser::$admin)
			return;

		$ti = new TextInfo($f);
		if (!isset($ti->author)) { $ti->author = CurrentUser::$account->login; }
		
		echo "<script>
		$(\"#button_clean\").click(function(){
		$(\"#ti_delete\").submit();
		});
		</script>";
		
		echo "<div class='section'><h2>Information</h2>\n";
		echo "<form action='?a=Tis&f=".File::a2r($f)."' method='post'>\n";
		echo "<fieldset>
				<div class='fieldname'>
					<span>".Settings::_("textinfo","title")."</span>
				</div>
				<div class='fieldoptions'>
					<input type='text' name='title' value='$ti->title' />
				</div>
			</fieldset>\n";
		echo "<fieldset>
				<div class='fieldname'>
					<span>".Settings::_("textinfo","name")."</span>
				</div>
				<div class='fieldoptions'>
					<input type='text' name='author' value='$ti->author' />
				</div>
			</fieldset>\n";
		echo "<fieldset>
				<div class='fieldname'>
					<span>".Settings::_("textinfo","explain")."</span>
				</div>
				<div class='fieldoptions'>
					<textarea style='border:0;' name='contain'>$ti->contain</textarea>
				</div>
			</fieldset>\n";
		echo "<fieldset class='alignright'>\n
				<input type='button' id='button_clean' class='button blue' value='".Settings::_("textinfo","delete")."' />
				<input type='submit' class='button blue' value='".Settings::_("settings","submit")."' />
			</fieldset>\n";
		echo "<input type='hidden' name='f' value='$f' />\n";
		echo "</form>";
		echo "<form id='ti_delete' action='?a=Tid&f=".File::a2r($f)."' method='post'>\n";		
		echo "<input type='hidden' name='f' value='$f' />\n";
		echo "</form>";
		echo "</div>\n";
	}
	
	/**
	 */
	public function toHTML(){
		
		if(CurrentUser::$admin) {
			echo "<div  class='textinfoadmin'>\n";
			self::Edit_File($this->file);
			echo "</div>\n";
		}
		
		if (is_file($this->path) && !empty($this->contain) )  {
			echo "<div  class='textinfo'>\n";
			echo "<span>".nl2br($this->contain)."<p style='font-size:12px;text-align:right'>$this->author</p></span>";
			echo "</div>\n";
		}
	}
}
?>
