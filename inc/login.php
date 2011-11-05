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

if(is_file('login.php')) chdir('..');

require_once 'src/secu.php';

$res="";

if(isset($_SESSION['login'])){
	log_me_out();
	$res="Logged out.";
}

if(isset($_POST['login']) && isset($_POST['pass'])){
	if(!log_me_in($_POST['login'],$_POST['pass'])){
		$res="Wrong login.";
	}else{
		echo "You are now logged in as ".$_SESSION['login'];
		exit();
	}
}

?>

<div class='inc_title'>Login</div>

<div class='result'>
<br><?php echo $res; ?></br>	
</div>
<form method="post" action="#" class='niceform'>
	<table>
	<tr>
		<td>Login : </td>
		<td><input type='text' name='login'></td>
	</tr>
	<tr>
		<td>Password : </td>
		<td><input type='password' name='pass'></td>
	</tr>
	</table>
	<input type="submit" value="Login" class='button blue'> or <a href="?f=register">register</a>
</form>