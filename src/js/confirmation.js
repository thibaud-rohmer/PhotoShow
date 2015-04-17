/**
 * This file implements a confirmation dialog.
 * 
 * Javascript
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	  PhotoShow
 * @category  Website
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */

function executeOnSubmit(button)
{
	switch(button){
		case 'rename':
		case 'create':
		case 'download':
		case 'delete':
			var res = confirm("Do you really wish to "+button+" this item?")
			break;
		case 'permissions':
			var res = confirm("Do you want to change Permissions?")
			break;
		case 'token':
			var res = confirm("Do you want to create a guest token?")
			break;
	};
    if(res)
		return true;
   	else
       	return false;
}