<?php
/**
 * This file implements the class JS.
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
 * JS
 *
 * JS Support.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
class JS extends Page
{
	private $toPrint;

	private $j;

	public function __construct(){

		/// Execute stuff automagically
		new Admin();

		if(isset($_GET['j'])){
			switch($_GET['j']){

				case "Pag":		$m = new Menu();
								$p = new Board();
								$ap = new AdminPanel();

								echo "<div id='menu' class='menu'>\n";
								
								$m->toHTML();

								echo "</div>\n";
								echo "<div class='panel'>\n";
								$p->toHTML();
								echo "</div>\n";

								echo "<div class='image_panel hidden'>\n";
								echo "</div>\n";

								if(CurrentUser::$admin){
									echo "<div class='infos'>\n";
									$ap->toHTML();
									echo "</div>\n";
								}
								break;

				case "Log":		$p = new LoginPage();
								$p->toHTML();
								break;
				
				case "Reg":		$p = new RegisterPage();
								$p->toHTML();
								break;

				case "Pan":		if(is_file(CurrentUser::$path)){
									$b = new ImagePanel(CurrentUser::$path);
									$b->toHTML();
								}else{
									$b = new Board(CurrentUser::$path);
									$b->toHTML();
								}
								break;

				case "Men":		$m = new Menu();
								$m->toHTML();
								break;


				case "Pan":		$f = new AdminPanel();
								$f->toHTML();
								break;

				case "Inf":		$f = new Infos();
								$f->toHTML();
								break;

				case "Jud":		$j = new Judge(CurrentUser::$path);
								$j->toHTML();
								break;
				
				case "Acc": 	$f = new Group();
								$f->toHTML();
								break;
				
				case "Comm":	$f = new Comments(CurrentUser::$path);
								$f->toHTML();
								break;

				default:		break;
			}
		}
	}

	public function toHTML(){
		
	}
}


?>