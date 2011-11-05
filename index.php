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


require_once 'src/layout.php';
require_once 'src/settings.php';
require_once 'src/secu.php';

$settings=get_settings();
$action=parse_action($_GET['f']);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $settings['site_name']; ?></title>
	<meta name="author" content="Thibaud Rohmer">
	<link href='http://fonts.googleapis.com/css?family=Quicksand:300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="src/stylesheet.css" type="text/css" media="screen" charset="utf-8">
	
<?php
if($action['layout']=="image"){
	echo "<style>\n .layout_thumbs{\n display:none;\n }\n </style>\n";
}else{
	echo "<style>\n .layout_image{\n display:none;\n }\n </style>\n";
}
?>

</head>
<body>
<div id="container">
	<div class="layout_thumbs">
		<div id="menu">
			<?php 
				menu($action['dir'],$action['subdir']); 
			?>
		</div>
	</div>
	<?php
		$layout=$action['layout'];
		echo 	"<div id='boards_panel' class='boards_panel_$layout'>";
		$info=board($action['display']); 
		echo 	"</div>";
	?>
	<div class="layout_image">
		<div id="top">
			<div id="exif" class='box'>
				<?php
					require("inc/exif.php");
				?>
			</div>

			<?php
				$image="";
				if($action['layout']=="image") {
					$image	=	"src/getfile.php?file=".relative_path($action['display'],$settings['photos_dir']);
				}
			?>
			
			<div id="center">
				<div id="image_big" style="background: black url('<?php echo $image; ?>') no-repeat center center; background-size: contain;">
				<?php 
					echo"<a href='?f=".htmlentities(dirname($_GET['f']))."'>"; 
				?>
				<image src="inc/img.png" height="100%" width="100%" style="opacity:0;"></a>
				</div>

				<div id="bar">
					<?php 
					foreach($info as $inf=>$val){
						echo "<div id='$inf' class='bar_button'><a href='?f=$val'>$inf</a></div>";
					}
					?>
				</div>
			</div>
			
			<div id="comments" class='box'>
				<?php
					require("inc/comments.php");
				?>			
			</div>
		</div>
		<div id="thumbs_bottom">
		</div>
	</div>
</div>
</body>
</html>
