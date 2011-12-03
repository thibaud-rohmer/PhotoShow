<?php
/**
 * This file implements the class JS Accounts.
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
 * JS Accounts
 *
 * Form for editing accounts. With JS.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class JSAccounts
{

	/// The accounts
	private $accounts;

	/// The groups
	private $groups;



	public function __construct(){
		$this->accounts = Account::findAll();

		$this->groups = Group::findAll();
	}

	public function toHTML(){
		$groupaccounts = array();

		echo "<div class='leftcolumn'>";
		echo "<h1>Accounts</h1>";
		foreach($this->accounts as $acc){
			echo "<div class='accountitem'>
						<div class='delete'>
							<form action='?t=Adm&a=ADe' method='post'>
								<input type='hidden' name='name' value='".htmlentities($acc['login'])."'>
								<input type='submit' value='x'>
							</form>
						</div>";
			echo "<div class='name'>".$acc['login']."</div>";
			foreach($acc['groups'] as $g){
				$groupaccounts["$g"][] = $acc['login'];
				echo "<div class='inlinedel'><span class='rmgroup'>x</span><span class='groupname'>".htmlentities($g)."</span></div>";
			}
			echo "</div>";
		}
		echo "</div>";

		echo "<div class='rightcolumn'>";
		echo "<h1>Groups</h1>";

		echo "<div class='newgroup'>";
		echo "
		<form class='addgroup' type='post' action='?t=Adm&a=GC'>
			<fieldset>
			<span>Add group</span>
			<div><input type='text' name='group' value='Group Name' /></div>
			</fieldset>
			<fieldset><input type='submit' value='Create'></fieldset>
			</form>\n";
		echo "</div>";

		foreach($this->groups as $g){
			$gn = $g['name'];
			echo "<div class='groupitem'>
						<div class='delete'>
							<form action='?t=Adm&a=GDe' method='post'>
								<input type='hidden' name='name' value='$gn'>
								<input type='submit' value='x'>
							</form>
						</div>";
			echo "<div class='name'>".$gn."</div>";

			if(isset($groupaccounts["$gn"])){
				foreach($groupaccounts["$gn"] as $g){
					echo "<div class='inlinedel'><span class='rmacc'>x</span><span class='accname'>".htmlentities($g)."</span></div>";
				}
			}
			echo "</div>";
		}
		
		echo "</div>";
	}

}