<?php
/**
 * This file implements the class Judge.
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
 * Judge
 *
 * The Judge verifies the rights of Current User, and checks
 * if he is allowed to reach some content. No one fools the
 * Judge. After all, the Judge is the Law.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Judge
{
	/// Absolute path to rights file for requested file
	public $path;
	
	/// True if requested file is public
	public $public=false;
	
	/// Groups allowed to see requested file
	public $groups=array();
	
	/// Users allowed to see requested file
	public $users=array();
	
	/// Name of requested file
	public $filename;

	/// Urlencoded relative path
	public $webpath;

	/// Path to the file
	public $file;

	/// Are we working with multiple items ?
	private $multi;

	/**
	 * Create a Judge for a specific file.
	 *
	 * @param string $f 
	 * @param string $read_rights 
	 * @author Thibaud Rohmer
	 */
	public function __construct($f, $read_rights=true){

		if(! is_array($f) && !file_exists($f) ){
			return;
		}
		$this->public	=	false;
		$this->groups	=	array();
		$this->users	=	array();
		$this->file 	=	$f;

		// Multiple files
		if(is_array($f)){
			$this->multi = true;
			$this->filename = sizeof($f) . " files selected";
			$this->webpath = "";
			foreach ($f as $file) {
				$this->webpath .= "&f[]=".urlencode(File::a2r($file));
			}
		}else{
			$this->multi = false;
			$this->set_path($f);
			if($read_rights){
				$this->set_rights();
			}
		}
	}
	
	/**
	 * Get path to rights file associated to our file
	 *
	 * @param string $f 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function set_path($f){
		
		$basefile	= 	new File($f);
		$basepath	=	File::a2r($f);

		$this->filename = $basefile->name;
		$this->webpath 	= "&f=".urlencode($basepath);

		if(is_file($f)){
			$rightsfile	=	dirname($basepath)."/.".basename($f)."_rights.xml";
		}else{
			$rightsfile	=	$basepath."/.rights.xml";
		}
		$this->path =	File::r2a($rightsfile,Settings::$thumbs_dir);

	}
	
	/**
	 * Get rights (recursively) for the file
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	private function set_rights(){

		/// First, parse the rights file (if it exists)
		try{
			$xml_infos	=	new File($this->path);
			$xml		=	simplexml_load_file($this->path);

			$this->public	=	($xml->public == 1);

			foreach($xml->groups->children() as $g)
				$this->groups[]=(string)$g;

			foreach($xml->users->children() as $u)
				$this->users[]=(string)$u;

		}catch(Exception $e){
		
			/// If no rights file found, check in the containing directory
			try{
				// Look up

				$up		=	dirname($this->file);
				$j = new Judge($up);
				
				$this->groups 	= $j->groups;
				$this->users 	= $j->users;
				$this->public 	= $j->public;


			}catch(Exception $e){
				
				// We are as high as possible
				$this->public	=	false;
				$this->groups	=	array();
				$this->users	=	array();		
			}
		}
	}

	/**
	 * Returns path to associated file
	 */
	public static function associated_file($rf){
		$associated_dir = File::r2a(File::a2r(dirname($rf),Settings::$thumbs_dir),Settings::$photos_dir);
		if(basename($rf) == ".rights.xml"){
			return $associated_dir;
		}else{
			return $associated_dir."/".substr(basename($rf),1,-11);
		}		
	}


	/**
	 * Check if a file is viewable in a folder, and returns path to that file.
	 */
	public static function searchDir($dir,$public_search = false){
		$rightsdir = File::r2a(File::a2r($dir),Settings::$thumbs_dir);
		$rightsfiles=glob($rightsdir."/.*ights.xml");

		// Check files
		if(!isset($rightsfiles) || count($rightsfiles) < 1 ){
			$rightsfiles = NULL;
		}

		foreach($rightsfiles as $rf){
			$f = Judge::associated_file($rf);
            if(($public_search and Judge::is_public($f))
                or (!$public_search and Judge::view($f))){
                    if(is_file($f)){
                        return $f;
                    }else{
                        foreach(Menu::list_files($f,true) as $p){
                            if(($public_search and Judge::is_public($p))
                                or (!$public_search and Judge::view($p))){
                                    return $p;
                                }
                        }
                    }
                }
        }

		// Check subdirs
		foreach(Menu::list_dirs($dir) as $d){
			if(($f=Judge::searchDir($d, $public_search))){
				return $f;
			}
		}

		return false;
	}

	/**
	 * Save our judge for this file as an xml file
	 *
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public function save(){
		
		/// Create xml
		$xml		=	new SimpleXMLElement('<rights></rights>');
		
		/// Put values in xml
		$xml->addChild('public',$this->public);
		$xml_users	=	$xml->addChild('users');
		$xml_groups	=	$xml->addChild('groups');

		foreach($this->users as $user)
			$xml_users->addChild("login",$user);

		foreach($this->groups as $group)
			$xml_groups->addChild("group",$group);
		
		if(!file_exists(dirname($this->path))){
			@mkdir(dirname($this->path),0750,true);
		}
		/// Save xml
		$xml->asXML($this->path);
	}
	
	/**
	 * Edit rights of the Judge. Because you can.
	 *
	 * @param string $f 
	 * @param string $groups 
	 * @param string $users 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	public static function edit($f,$users=array(),$groups=array(),$private=false){

		/// Just to be sure, check that user is admin
		if(!CurrentUser::$admin)
			return;

		if(is_array($f)){
			foreach($f as $file){
				Judge::edit($file,$users,$groups,$private);
			}
			return;
		}
		// Create new Judge, no need to read its rights
		$rights			=	new Judge($f,false);

		/// Put the values in the Judge (poor guy)
		if(isset($groups)){
			$rights->groups =	$groups;
		}

		if(isset($users)){
			$rights->users =	$users;
		}
		
		$rights->public	=	( !$private ) ? 1 : 0;
		
		// Save the Judge
		$rights->save();
	}
	
	/**
	 * Returns true if the file to access is in the sub-path of the main directory
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function inGoodPlace($f){

		$rf =	realpath($f);
		$rd =	realpath(Settings::$photos_dir);
		
		if($rf == $rd) return true;

		if( substr($rf,0,strlen($rd)) == $rd ){
			return true;
		}
		return false;

	}

	/**
	 * Returns true if the current user may access this file
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Thibaud Rohmer
	 */
	public static function view($f){
		
		// Check if user has an account		
		if(!isset(CurrentUser::$account) && !isset(CurrentUser::$token)){
			// User is not logged in
			$judge	=	new Judge($f);
			return($judge->public);
		}

		if(!Judge::inGoodPlace($f))
			return false;

		// No Judge required for the admin. This guy rocks.
		if(CurrentUser::$admin)
			return true;

		// Create Judge
		$judge	=	new Judge($f);
		
		// Public file
		if($judge->public){
			return true;
		}

        if (isset(CurrentUser::$account)){
            // User allowed
            if(in_array(CurrentUser::$account->login,$judge->users)){
                return true;
            }

            // User in allowed group
            foreach(CurrentUser::$account->groups as $group){
                if(in_array($group,$judge->groups)){
                    return true;
                }
            }
        }
        if (isset(CurrentUser::$token)){
            if (GuestToken::view(CurrentUser::$token,$f)){
                return true;
            }
        }
        
		return false;
	}

	/**
	 * Returns true if the file is public
	 *
	 * @param string $f file to access
	 * @return bool
	 * @author Franck Royer
	 */
    public static function is_public($f){
        $judge	=	new Judge($f);
        return($judge->public);
    }

	/**
	 * Display the rights on website, and let
	 * the admin edit them.
	 * 
	 * @author Thibaud Rohmer
	 */
	public function toHTML(){
		
		echo "<div class='adminrights'>\n";
		echo "<div class='section'>";

		echo "<h2>".htmlentities($this->filename, ENT_QUOTES ,'UTF-8')."</h2>\n";


		if(!$this->multi){
			if($this->public){

				echo "<form action='?t=Pri$this->webpath' method='post'>\n";
				echo Settings::_("judge","public");
				echo "<fieldset><input type='submit' class='button blue' value='".Settings::_("judge","gopriv")."' /></fieldset>";
				echo "</form>";
				echo "</div>";
				return;

			}else{
				echo "<form action='?t=Pub$this->webpath' method='post'>\n";
				echo Settings::_("judge","priv");
				echo "<fieldset><input type='submit' class='button blue' value='".Settings::_("judge","gopub")."' /></fieldset>";
				echo "</form>";
			}
		}else{
				echo "<form action='?t=Pub$this->webpath' method='post'>\n";
				echo "<fieldset><input type='submit' class='button blue' value='"."Set those items as Public"."' /></fieldset>";
				echo "</form>";
		}

		echo "<form action='?t=Rig$this->webpath' method='post'>\n";
		echo "<h2>".Settings::_("judge","accounts")."</h2>";

		foreach(Account::findAll() as $account){
			
			if(in_array($account['login'], $this->users)){
				$checked = "checked";
			}else{
				$checked = "";
			}

			echo "<div><label><input type='checkbox' value='".$account['login']."' name='users[]' $checked >".htmlentities($account['login'], ENT_QUOTES ,'UTF-8')."</label></div>";
		}

		echo "<h2>".Settings::_("judge","groups")."</h2>";

		foreach(Group::findAll() as $group){
			if($group['name'] == "root"){
				continue;
			}
			if(in_array($group['name'], $this->groups)){
				$checked = "checked";
			}else{
				$checked = "";
			}

			echo "<div><label><input type='checkbox' value='".$group['name']."' name='groups[]' $checked > ".htmlentities($group['name'], ENT_QUOTES ,'UTF-8')." </label></div>";
		}

		echo "<fieldset><input type='submit' class='button blue' value='".Settings::_("judge","set")."'></fieldset>\n";
		echo "</form>\n";
        
        if(!$this->multi){
	        // Token creation
	        echo "<h2>".Settings::_("token","tokens")."</h2>\n";
	        $tokens = GuestToken::find_for_path($this->file);
	        if ($tokens && !empty($tokens)){
	            foreach($tokens as $token){
	                echo "<a href='".GuestToken::get_url($token['key'])."' >".$token['key']."<\a><br />\n";
	            }
	        }
	        echo "<form action='?t=CTk$this->webpath' method='post'>\n";
	        echo "<fieldset><input type='submit' class='button blue' value='".Settings::_("token","createtoken")."' /></fieldset>";
	        echo "</form>";
			echo "</div>";
		}

        echo "</div>\n";
    }


}
?>
