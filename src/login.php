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

require_once realpath(dirname(__FILE__).'/secu.php');
require_once realpath(dirname(__FILE__).'/settings.php');


/**
 * Login !
 *
 * 	\param string $login
 * 		Login
 * 	\param string $pass
 * 		Pass
 */
function log_me_in($login,$pass,$crypted=false){
	// Load accounts file into xml
	$file=accounts_file();
	$xml=simplexml_load_file($file);
	
	// Look for the account
	foreach($xml as $acc){
		if($acc->login==$login){
			if(($crypted && $acc->pass == $pass) OR (!$crypted && $acc->pass == sha1($pass))){
				$_SESSION['login']	=	$login;
				$xmlgrp				=	$acc->groups->children();
				foreach ( $xmlgrp as $g ){
					$_SESSION['groups'][]	=	(string) $g;
				}
				return true;
			}
		}
	}
	return false;
}


/**
 * 	Returns true if the user is an admin.
 */
function admin(){
	return (isset($_SESSION['login']) && in_array("root",$_SESSION['groups']));
}

/**
 * Logout
 */
function log_me_out(){
	unset($_SESSION['login']);
	unset($_SESSION['groups']);
}


?>