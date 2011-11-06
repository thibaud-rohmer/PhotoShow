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

require_once realpath(dirname(__FILE__).'/../../src/secu.php');
require_once realpath(dirname(__FILE__).'/../../src/settings.php');
require_once realpath(dirname(__FILE__).'/../../src/layout.php');

// If we aren't logged, or aren't an admin, we go back to index.
if(!admin()){
	echo "You aren't supposed to be there.";
	exit();
}

if(isset($_POST['action'])){
	if($_POST['action']=="delete"){
		delete_accounts($_POST['users']);
	}
	if($_POST['action']=="edit"){
		foreach ($_POST['users'] as $l){
			echo "<br><a href='?f=users&a=$l'>Edit $l</a></br>";
		}
	}
}

if(isset($_GET['a'])){
	require_once realpath(dirname(__FILE__).'/../user.php');
	exit();
}
?>

<form method="post" action="#" class="niceform">
<div class="admin_box">
	<div class="admin_box_title">User</div>
	<div class="admin_box_content">

		<?php
			$users=get_logins();
			foreach($users as $user){
				echo "<br><label><input type='checkbox' name='users[]' value='$user'> $user</label></br>";
			}
		?>
	</div>
</div>
<div class="admin_box">
	<div class="admin_box_title">Action</div>
	<div class="admin_box_content">
			<?php
			$possible_action=array('delete','edit');
			foreach($possible_action as $pa){
				echo "<br><label><input type='radio' name='action' value='$pa'/> $pa</label></br>";
			}
		?>
	</div>
</div>	

<input type="submit" value="OK" class="button blue">
</form>
