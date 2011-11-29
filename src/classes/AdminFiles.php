<?php
/**
 * This file implements the class AdminFiles.
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
 * Admin Files
 *
 * Display the forms for the admin.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow-v2
 */
class AdminFiles
{
	/// Delete form
	private $delete;

	/// Move form
	private $move;

	/// Upload form
	private $upload;

	/// Awesome JS form
	private $JS;


 	/**
 	 * Initialise variables
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){
 		$this->delete 	= new AdminDelete();
 		$this->move 	= new AdminMove();
 		$this->upload 	= new AdminUpload();
 		$this->JS 		= new JSFiles();
 	}


 	public function toHTML(){
 		echo "<noscript>";
 		echo "<div class='panel'>";
 		$this->upload->toHTML();
 		$this->move->toHTML();
 		$this->delete->toHTML();
 		echo "</div>";
 		echo "</noscript>";
 		echo "<div class='noscript_hidden'>";
 		$this->JS->toHTML();
 		echo "</div>";
 	}


}