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

$view=who_can_view($file);

$allowed_users	=	$view['users'];
$allowed_groups	=	$view['groups'];

$public_checked='';
if(sizeof($allowed_groups)==0 && sizeof($allowed_users)==0){
	$public=true;
	$public_checked='checked';
}
?>

<div class='box_title'>Rights</div>
<form>
<?php
echo "<table class='table_rights'>";
if($public) echo "<li>This item is Public</li>\n";
echo "<tr><td class='td_data'>Groups</td><td></td></tr>\n";
foreach(get_groups() as $group){
	$checked='checked';
	if(!$public)
		$checked=in_array($group,$allowed_groups)?'checked':'';
	echo "<tr><td></td><td><label><input type='checkbox' name='rights_users' $checked> $group</label></td></tr>\n";
}
echo "<tr><td class='td_data'>Users</td><td></td></tr>\n";
foreach(get_logins() as $user){
	$checked='checked';
	if(!$public)
		$checked=in_array($user,$allowed_users)?'checked':'';
	echo "<tr><td></td><td><label><input type='checkbox' name='rights_groups' $checked> $user</label></td></tr>\n";
}
?>

<tr style="text-align:center;"><td colspan="2"><input type="submit" value="Apply" class='button blue'></td></tr>
</table>
</form>

