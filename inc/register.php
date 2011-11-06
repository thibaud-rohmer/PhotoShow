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

$res=array();

if(isset($_POST['login'])){
	if(!isset($_POST['mail'])|| !isset($_POST['pass'])){
		$res[]="Please fill in all fields";
	}else{
		$login	=	$_POST['login'];
		$mail	=	$_POST['mail'];
		$pass	=	$_POST['pass'];
		if(strlen($login) < 3 || !preg_match("/([a-zA-Z0-9])/",$login)){
			$res[]="Login must be at least 3 letters long, and only contain letters, numbers, dash, dot";
		}
		if(strlen($pass)<6){
			$res[]="Password must be at least 6 characters";
		}
		if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $mail)){
			$res[]="Email is not valid";
		}

		
		if(sizeof($res)==0){
			$more['email']=$mail;
			if(!add_account($login,$pass,array(),$more)){
				$res[]="Login already taken";
			}else{
				require realpath(dirname(__FILE__).'/../inc/login.php');
				exit();
			}
		}
	}
}

if(!is_file($settings['thumbs_dir']."/accounts.xml")){
	$inc_title="Please create the main account";
}else{
	$inc_title="Register";
}

echo "<div class='inc_title'>$inc_title</div>";
?>



<div class='resuls'>
<?php

if(sizeof($res>0)){
	foreach($res as $r){
		echo "<br>$r</br>";
	}
}

?>
</div>

<form method="post" action="#" class='niceform'>
	<table>
	<tr>
		<td>Login : </td>
		<td><input type='text' name='login'></td>
	</tr>
	<tr>
		<td>E-mail : </td>
		<td><input type='text' name='mail'></td>
	</tr>
	<tr>
		<td>Password : </td>
		<td><input type='password' name='pass'></td>
	</tr>
	</table>
	<input type="submit" value="Register" class='button blue'>
</form>