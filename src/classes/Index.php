<?php
/**
 * This file implements the class Index.
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
 * Index
 *
 * Now, what could this possibly be ? Oh, right... It's the
 * Index. Who would've guessed, eh ?
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Index
{
	function __construct(){
		/// Initialize variables
		Settings::init();


		/// Initialize CurrentUser
		try{
			CurrentUser::init();
		}catch(Exception $e){
			$page = new RegisterPage(true);
			$page->toHTML();
			return;
		}

		/// Check what to do
		switch (CurrentUser::$action){

			case "Rss":		$r = new RSS(Settings::$conf_dir."/photos_feed.txt");
							$r->toXML();
							break;

			case "Judge":	// Same as page
			case "Page":	$page = new MainPage();
							$page->toHTML();
							break;
			
			case "Logout":
			case "Login":			
			case "Log":		$page = new LoginPage();
							$page->toHTML();
							break;
							
			case "Reg":		$page = new RegisterPage();
							$page->toHTML();
							break;

			case "JS":		$page = new JS();
							break;

			case "Img":		Provider::Image(CurrentUser::$path);
							break;
			
			case "BDl":		Provider::Image(CurrentUser::$path,false,true,true,true);
							break;

			case "Big":		Provider::Image(CurrentUser::$path,false,true);
							break;

			case "Thb":		Provider::Image(CurrentUser::$path,true);
							break;

			case "Vid":		Provider::Video(CurrentUser::$path);
							break;
						
			case "Zip":		Provider::Zip(CurrentUser::$path);
							break;
			
			case "Acc":		if(CurrentUser::$admin && isset($_POST['login'])){
								$acc = new Account($_POST['login']);
							}else{
								$acc = CurrentUser::$account;
							}
							$acc->toHTML();
							break;
			
			case "Adm":		$page = new Admin();
							$page->toHTML();
							break;
		}
	}
}

?>
