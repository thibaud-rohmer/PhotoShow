<?php
/**
 * This file implements the class API.
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
 * API
 *
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class API
{

	function __construct(){
		require_once dirname(__FILE__).'/../XRL/src/Autoload.php';

		/// Initialize variables
		Settings::init();

		/// Initialize CurrentUser
		CurrentUser::init();


		$this->server = new XRL_Server();
		$this->server["XRL_DecoderFactoryInterface"] = new XRL_NonValidatingDecoderFactory();

		foreach(get_class_methods('API') as $c){
			if($c == '__construct')
				continue;
			$this->server->$c = array('API',$c);	
		}
	
		$this->server->foo = array('API','foo');
		$this->server->handle()->publish();	
	}


	/**
	 * Basic test function
	 */
	public static function foo($bar){
		return $bar + 42;
	}

	/**
	 * Returns key associated to user
	 */
	public static function get_key($login,$pass){
		if(CurrentUser::login($login,$pass)){
			return CurrentUser::$account->get_key();
		}else{
			return false;
		}
	}

	/**
	 * Returns account info associated to $key
	 */
	public static function whoami($key){
		if(CurrentUser::keyin($key)){
			return CurrentUser::$account->get_acc();
		}else{
			return false;
		}
	}

	/**
	 * List directories contained in $dir
	 */
	public static function list_dirs($key,$dir,$rec=false){
		CurrentUser::keyin($key);

		$res = array();
		
		$m = Menu::list_dirs(File::r2a($dir),$rec);
		
		if(sizeof($m) == 0){
			return $res;
		}
		
		foreach($m as $i){
			if(Judge::view($i)){
				$res[] = File::a2r($i);
			}
		}
		return $res;
	}

	/**
	 * List files contained in $dir
	 */
	public static function list_files($key,$dir){
		CurrentUser::keyin($key);

		$res = array();

		$m = Menu::list_files(File::r2a($dir));
		if(sizeof($m) == 0){
			return $res;
		}

		foreach($m as $i){
			if(Judge::view($i)){
				$res[] = File::a2r($i);
			}
		}
		return $res;
	}

	/**
	 * Return image(s) $img
	 */
	public static function get_img($key,$img,$t='large'){
		if(is_array($img)){
			$res = array();
			foreach($img as $i){
				$p = get_img($key,$i,$t);
				if(isset($p)){
					$res[] = $p;
				}
			}
			return $res;
		}else{
			$i=File::r2a($img);
			if(Judge::view($i)){
				switch($t){
					case("thumb"):	return file_get_contents(Provider::thumb($i));
					case("small"):	return file_get_contents(Provider::small($i));
					case("large"):
					default:
							return file_get_contents($i);
				}
			}
		}
	}
}

?>
