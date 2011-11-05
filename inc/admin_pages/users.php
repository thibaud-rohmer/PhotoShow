<?php
/*
    This file is part of PhotoShow.

    PhotoShow is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhotoShow is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
*/

if(file_exists('users.php')) chdir('../..');

require_once 'src/secu.php';
?>



<form method="post" action="#" class="niceform">
	
<table>
	<tr>
		<td>
			User</br>
			<?php
				$users=get_logins();
				foreach($users as $user){
					echo "<br><label><input type='checkbox' name='users[]' value='$user'> $user</label></br>";
				}
			?>
		</td>
		<td>
			Action</br>
				<?php
				$possible_action=array('delete','edit');
				foreach($possible_action as $pa){
					echo "<br><label><input type='radio' name='action' value='$pa'/> $pa</label></br>";
				}
				?>
		</td>
	</tr>
</table>
<input type="submit" value="OK" class="button blue">
</form>