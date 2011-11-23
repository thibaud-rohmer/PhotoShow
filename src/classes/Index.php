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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

class Index
{
	function __construct(){

		/// Initialize variables
		try{
			Settings::init();
		}catch(Exception $e){
			echo $e;
			return;
			// Settings file is missing, or directories
			// are missing... Bad conf.
		}

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

			case "Page":	$page = new MainPage();
							$page->toHTML();
							break;
							
			case "Log":		$page = new LoginPage();
							$page->toHTML();
							break;
							
			case "Reg":		$page = new RegisterPage();
							$page->toHTML();
							break;

			case "Img":		Provider::Image(CurrentUser::$path);
							break;

			case "Thb":		Provider::Image(CurrentUser::$path,true);
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