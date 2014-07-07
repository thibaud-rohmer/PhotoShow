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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
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
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

 class AdminStats
 {

 	// Stats
 	private $stats = array();

 	// Stats
 	private $accounts = array();

 	// Comments
 	private $comments = array();

 	/**
 	 * Calculate stats of the website
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 	}
	
	public function Calculate() {
	
		/// Calculate number of users, etc...
 		$this->stats['Users'] = sizeof(Account::findAll());

 		$this->stats['Groups'] = sizeof(Group::findAll());

 		$this->stats['Items'] = sizeof(Menu::list_files(Settings::$photos_dir,true));

 		$this->stats['Generated items'] = sizeof(Menu::list_files(Settings::$thumbs_dir,true));

 		$this->stats['Albums'] = sizeof(Menu::list_dirs(Settings::$photos_dir,true));

 		$this->accounts = array_reverse(Account::findAll());

 		$commentsfile = Settings::$conf_dir."/comments.xml";

 		if(is_file($commentsfile)){
 			$xml = simplexml_load_file($commentsfile);
 			$this->comments = $xml->children();
 		}
	
	}

 	public function toHTML(){
		self::Calculate() ;

 		echo "<div class='header'>";
 		echo "<h1>Statistics</h1>";
 		echo "</div>";

 		echo "<h2>Stats</h2>";
 		echo "<ul>";
 		echo "<table class='pure-table pure-table-striped'>";
 		echo "<tbody>";
 		foreach($this->stats as $name=>$val){
 			echo "<tr><td>".htmlentities($name, ENT_QUOTES ,'UTF-8')."</td><td>".htmlentities($val, ENT_QUOTES ,'UTF-8')."</td></tr>"; 			
 		}
 		echo "</tbody>";
 		echo "</table>";
 		echo "</ul>";


 		echo "<h2>Accounts (by age)</h2>";
 		echo "<ul>";
 		echo "<table class='pure-table pure-table-striped'>";
 		echo "<tbody>";
 		foreach($this->accounts as $acc){
 			echo "<tr><td>".htmlentities($acc['login'], ENT_QUOTES ,'UTF-8')."</td></tr>"; 			
 		}
 		echo "</tbody>";
 		echo "</table>";
 		echo "</ul>";


 		echo "<h2>Comments (by age)</h2>";
 		echo "<ul>";
 		echo "<table class='pure-table pure-table-striped'>";
 		echo "<tbody>";

		$len = sizeof($this->comments);

		for($i=$len - 1;$i >= 0; $i--){
			$c = $this->comments[$i];
 			echo "<tr>
 					<td><a href=\"?f=".htmlentities($c->webfile)."\">".htmlentities($c->path, ENT_QUOTES ,'UTF-8')."</a></td>
 					<td>".htmlentities($c->login, ENT_QUOTES ,'UTF-8')."</td>
 					<td>".htmlentities($c->content, ENT_QUOTES ,'UTF-8')."</td>
 				</tr>";
 		}

 		echo "</tbody>";
 		echo "</table>";
 		echo "</ul>";

 	}
 }

 ?>