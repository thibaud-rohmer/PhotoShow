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

require_once realpath(dirname(__FILE__).'/../src/secu.php');
require_once realpath(dirname(__FILE__).'/../src/settings.php');
require_once realpath(dirname(__FILE__).'/../src/layout.php');


if(!isset($_SESSION['login'])){
	echo "You have to be logged in !";
	return;
}

$login=$_SESSION['login'];

if(admin() && isset($_GET['a'])){
	$login=$_GET['a'];
}

$infos=array('login','name','email','telephone','website','new password','verify new password');

if(isset($_POST['login'])){
	$toedit=array();
	foreach($infos as $info){
		if(isset($_POST[$info])) $toedit[$info]=$_POST[$info];
	}

	edit_account($login,$toedit);
}

if(!($user=get_account($login))){
	echo "This account doesn't exist";
	return;
}

?>
<link rel="stylesheet" href="src/admin.css" type="text/css" media="screen" title="no title" charset="utf-8">

<div class='admin_box'>
	<div class='admin_box_title'>
		<?php echo $login; ?>
	</div>
	
	<div class='admin_box_content' style="max-height:none;">
		<form class='niceform' method='post' action='#'>
		<table>
	<?php

	foreach($infos as $info){
		$val=isset($user[$info])?$user[$info]:'';
		echo "<tr><td>$info</td><td><input type='text' name='".htmlentities($info)."' value='".htmlentities($val)."'></td></tr>";
	}
	
	?>
		<tr><td colspan='2' style='text-align:right'><input type='submit' class='button blue'></td></tr>
		</table>
		</form>
	</div>
</div>