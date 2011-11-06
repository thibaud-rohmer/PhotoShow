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
session_start();

require_once realpath(dirname(__FILE__).'/../src/secu.php');
require_once realpath(dirname(__FILE__).'/../src/settings.php');
require_once realpath(dirname(__FILE__).'/../src/layout.php');

// If we aren't logged, or aren't an admin, we go back to index.
if(!admin()){
	echo "You aren't supposed to be there.";
	exit();
}

$settings=get_settings();

$action		=	"main";
if(isset($_GET['f']))
	$action=$_GET['f'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $settings['site_name']; ?> - Admin</title>
	<meta name="author" content="Thibaud Rohmer">
	<link href='http://fonts.googleapis.com/css?family=Quicksand:300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="../src/stylesheet.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="../src/admin.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
</head>
<body>
<div id="menubar">
	<?php menubar_admin(); ?>
</div>
<div id="container">
		<div id="menu">
			<?php 
				admin_menu($action);
			?>
		</div>
		<div class="boards_panel_thumbs">
			<div id="admin_center">
			<?php
				require realpath(dirname(__FILE__)."/admin_pages/$action.php");
			?>
			</div>
		</div>
</div>
</body>
</html>
