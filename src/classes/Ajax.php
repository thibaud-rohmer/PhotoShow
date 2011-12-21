<?php
/**
 * This file implements the class Ajax.
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
 * Ajax
 */
class Ajax
{
	private $fieldId;

	private $fieldValue;
	
	private $response;

	public function __construct(){
		
		$this->fieldId = $_GET['fieldId'];
		$this->fieldValue = $_GET['fieldValue'];
		
		if(isset($_GET['extraData'])){
			switch($_GET['extraData']){
				case "userNotExists" 	: 	$account = new Account($this->fieldValue);
											$this->response = !$account->exists($this->fieldValue);
											break;
			}
		}
	}
	
	public function response(){
		$msg = '';
		if (isset($_GET['extraData']))
		{
		if ($this->response){
			$msg = Settings::_($_GET['extraData'], $_GET['extraData'] . '_ok');
			}
		else{
			$msg = Settings::_($_GET['extraData'], $_GET['extraData'] . '_nok');
			}
		}
		return json_encode(array($this->fieldId, $this->response, $msg));
	}
}