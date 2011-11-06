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

if(is_file('rights.php')) chdir('..');

require_once 'src/secu.php';
require_once 'src/settings.php';
$settings=get_settings();

if(!admin()) return;
if(!isset($_GET['f'])) return;

$file		=	$settings['photos_dir']."/".$_GET['f'];

$info_rights['users']	=	array();
$info_rights['groups']	=	array();

if(isset($_POST['users']))
	$info_rights['users']	=	$_POST['users'];
if(isset($_POST['groups']))
	$info_rights['groups']	=	$_POST['groups'];

if(isset($_POST['users'])||isset($_POST['groups'])){
	edit_rights($file,$info_rights);
}

$view=who_can_view($file);

$allowed_users	=	$view['users'];
$allowed_groups	=	$view['groups'];

if(sizeof($allowed_groups)==0 && sizeof($allowed_users)==0){
	$public=true;
}
?>

<div class='box_title'>Rights</div>
<form method='post' action='#'>
<?php
echo "<table class='table_rights'>";
if($public) echo "<tr style='text-align:center;'><td colspan='2'>This item is Public</td></tr>\n";
echo "<tr><td><table><tr><td class='td_data'>Groups</td><td></td></tr>\n";
foreach(get_groups() as $group){
	$checked='checked';
	if(!$public)
		$checked=in_array($group,$allowed_groups)?'checked':'';
	echo "<tr><td></td><td><label><input type='checkbox' name='groups[]' value='$group' $checked> $group</label></td></tr>\n";
}
echo "</table></td><td><table><td><tr><td class='td_data'>Users</td><td></td></tr>\n";
foreach(get_logins() as $user){
	$checked='checked';
	if(!$public)
		$checked=in_array($user,$allowed_users)?'checked':'';
	echo "<tr><td></td><td><label><input type='checkbox' name='users[]' value='$user' $checked> $user</label></td></tr>\n";
}
?>
</table>
<tr style="text-align:center;"><td colspan="2"><input type="submit" value="Apply" class='button blue'></td></tr>
</table>
</form>

