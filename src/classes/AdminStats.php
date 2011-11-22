<?php
/**
 * This file implements the class AdminStats.
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
 * AdminStats
 *
 * Stats of the website
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */

 class AdminStats
 {

 	// Stats
 	private $stats = array();

 	/**
 	 * Calculate stats of the website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 		/// Calculate number of users, etc...
 		$this->stats['Users'] = sizeof(Account::findAll());

 		$this->stats['Groups'] = sizeof(Group::findAll());

 		$this->stats['Items'] = sizeof(Menu::list_files(Settings::$photos_dir,true));

 		$this->stats['Generated items'] = sizeof(Menu::list_files(Settings::$thumbs_dir,true));

 		$this->stats['Albums'] = sizeof(Menu::list_dirs(Settings::$photos_dir,true));
 	}

 	public function toHTML(){
 		echo "<table>";
 		foreach($this->stats as $name=>$val){
 			echo "<tr><td>$name</td><td>$val</td></tr>"; 			
 		}
 		echo "</table>";
 	}
 }

 ?>