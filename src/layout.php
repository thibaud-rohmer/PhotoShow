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

require_once realpath(dirname(__FILE__).'/settings.php');
require_once realpath(dirname(__FILE__).'/listings.php');
require_once realpath(dirname(__FILE__).'/images.php');
require_once realpath(dirname(__FILE__).'/secu.php');


/**
 * Creates the main menu
 * 
 * \param string $dir
 * 		Main directory for the photos.
 * \param string $selected_dir
 *		Currently selected dir in the interface
 * \param string $selected_subdir
 *		Currently selected subdir in the interface
 */
function menu($selected_dir=".",$selected_subdir="."){
	$settings=get_settings();
	$dirlist = list_dirs($settings['photos_dir'],true);

	foreach ( $dirlist as $dir )
	{
		if(!right_path($dir)) continue;
		// Adding the 'selected' class to selected dir
		$class="menu_dir";
		$is_selected=true;
		if(same_path($dir,$selected_dir))
			$class = $class . " selected";
		else
			$is_selected=false;
		
		// Creating the item
		echo 	"<div class='menu_item'>\n";
		echo 	"<div class='$class'>";
		echo 	"<a href='?f=";
		echo 	relative_path($dir,$settings['photos_dir']);
		echo 	"'>";
		echo 	basename($dir);
		echo 	"</a></div>\n";
		
		// Listing directories contained in the item
		$subdirlist = list_dirs($dir,true);
		
		echo "<div class='$subdirclass'";
		if(!$is_selected)
			echo " style='display:none'; ";
		echo ">\n";
		
		foreach ( $subdirlist as $subdir ) 
		{
			if(!right_path($subdir)) continue;
			
			// Adding the 'selected' class to selected subdir
			$class="menu_subdir";
			if(same_path($subdir,$selected_subdir))
				$class = $class . " selected";
			
			// Creating the item
			echo "<div class='$class'>";
			echo "<a href='?f=";
			echo relative_path($subdir,$settings['photos_dir']);
			echo "'>";
			echo basename($subdir);
			echo "</a></div>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
	}
}

/**
 * Creates the admin menu
 *
 * 	\param string $selected
 *		Current admin page selected
 */
function admin_menu($selected){
	// All the files for admin are there
	$pages=list_files(realpath(dirname(__FILE__).'/../inc/admin_pages'));
	// Listing all pages
	foreach ( $pages as $page ){
		// Getting page name without extension
		$info = pathinfo($page);
		$pagename=basename($page,'.'.$info['extension']);
		
		$class="menu_dir";
		if($pagename == $selected){
			$class 	=	$class . " selected ";
		}
		echo 	"<div class='menu_item'>\n";
		echo 	"<div class='$class'>";
		echo 	"<a href='?f=$pagename'>$pagename</a>";
		echo 	"</a></div>\n";
		echo 	"</div>\n";
	}
	
	// Link to come back to the website
	echo 	"<div class='menu_item'>\n";
	echo 	"<div class='menu_dir'>";
	echo 	"<a href='..'>Back to website</a>";
	echo 	"</a></div>\n";
	echo 	"</div>\n";
}

/**
 * Locates currently selected file in a list
 * 
 * \param string $selected
 * 		Path to the selected file
 * \param array $filelist
 * 		List of paths
 */
function setup_info($selected,$filelist){
	$info		=	array();
	$settings	=	get_settings();
	
	for($i=0;$i<sizeof($filelist);$i++){
		if(same_path($selected,$filelist[$i])){
			if($i>0) 
				$info['previous']	=	relative_path($filelist[$i-1],$settings['photos_dir']);
			if($i+1<sizeof($filelist))
				$info['next']		=	relative_path($filelist[$i+1],$settings['photos_dir']);
		}
	}

	return $info;
}

/**
 * Generates the menubar
 */
function menubar(){
	// Display user name if logged in
	echo "<div class='align_left'>";
	echo "<div class='menubar-button'><a href='http://osi.6-8.fr/PhotoShow'>PhotoShow</a></div>";
	if(isset($_SESSION['login'])) echo "<div class='menubar-button'>- logged as <a href='?f=user'>".$_SESSION['login']."</a></div>";
	echo 	"</div><div class='align_right'>";
	// Is the user logged in ?
	if(!isset($_SESSION['login'])){
		echo 	"<div class='menubar-button'><a href='?f=login'>LOGIN/REGISTER</a></div>\n";
	}else{
		// Is the user an admin ?
		if(admin()){
			echo 	"<div class='menubar-button'><a href='inc/admin.php'>ADMIN</a></div>\n";
		}
		echo 	"<div class='menubar-button'><a href='?f=login'>LOGOUT</a></div>\n";
	}
	echo 	"<div class='menubar-button'><a href='?f=rss'>RSS <img src='./inc/rss.png' height='11px'</a></div>\n";
	echo 	"</div>";
}

/**
 * Generates the menubar for the admin pages
 */
function menubar_admin(){
	// Display user name if logged in
	echo "<div class='align_left'>";
	echo "<div class='menubar-button'><a href='http://osi.6-8.fr/PhotoShow'>PhotoShow</a></div>";
	if(isset($_SESSION['login'])) echo "<div class='menubar-button'>- logged as ".$_SESSION['login']."</div>";
	echo 	"</div><div class='align_right'>";
	// Is the user logged in ?
	echo 	"<div class='menubar-button'><a href='..'>Back</a></div>\n";
	echo 	"</div>";
}


/**
 * Generates the board header
 * 
 * \param string $dir
 * 		Directory of the board
 */
function board_header($dir){
	$settings	=	get_settings();
	$rp			=	relative_path($dir,$settings['photos_dir']);
	
	echo 	"<div class='board_header'><div class='board_title'>";
	echo 	basename($dir);
	echo 	"</div>\n";
	echo 	"<div class='align_left'>";
	echo 	"<div class='button blue'><a href='?f=$rp'>URL</a></div>\n";
	echo 	"<div class='button blue'><a href='inc/zip.php?f=$rp'>ZIP</a></div>\n";
	echo 	"</div>\n";
	if(admin()){
		echo 	"<div class='align_right'>";
		echo 	"<div class='button orange'><a href='inc/admin.php?f=upload'>Upload Photos</a></div>\n";
		echo 	"</div>\n";
	}
	echo 	"</div>\n";
}

/**
 * Creates a board, where thumbs are displayed
 * 
 * \param string $dir
 * 		Directory where to look
 */
function board($dir){
	$settings	=	get_settings();
	
	// Initialize info
	$info=array();
	$info['next']		=
	$info['previous']	=	relative_path($dir,$settings['photos_dir']);
	
	// Setup our parameters
	if(is_file($dir)){
		$selected=$dir;
		$dir=dirname($dir);
	}
	$filelist	=	list_files($dir,true);
	$dirlist	=	list_dirs($dir,true);
	$rp			=	relative_path($dir,$settings['photos_dir']);
	
	
	// Get the previous, current, and next images
	if(isset($selected))
		$info		=	setup_info($selected,$filelist);
	
	echo 	"<div class='board'>\n";
	

	// Creation of the header
	if(right_path($dir)){
		board_header($dir);
	}
	
	// Let's select only the images we can see
	$new_filelist=array();
	foreach($filelist as $image){
		if(right_path($image))
			$new_filelist[]=$image;
	}
	$filelist=$new_filelist;
	
	
	// Let's analyze the images
	$analyzed = analyze_images($filelist,8);
	
	// First, we display the thumbs
	echo 	"<div class='board_items'>";
	$i=0;
	foreach ($analyzed as $line){
		
		$numitems=sizeof($line);
		$sumitems=array_sum($line);
		echo "<div class='board_line $numitems-items'>";
		
		foreach($line as $item){
		
			$file=$filelist[$i];
			$rp2f	=	relative_path($file,$settings['photos_dir']);
			$width	=	$item * 90 / $sumitems;
			
			if($width>25)
				$getfile	=	"file=$rp2f";
			else
				$getfile	=	"t=thumb&file=$rp2f";
				
			echo 	"<div class='board_item'";
			echo 	"style=\" width:$width%; background: url('src/getfile.php?$getfile') no-repeat center center; background-size: cover;\">";
			echo 	"<a href='?f=$rp2f'><img src='./inc/img.png' width='100%' height='100%'></a></div>\n";
			$i++;
		
		}
		echo "</div>";		
	}
	echo 	"</div>";

	// Then, we display the sub-boards
/* -- No sub-boards at the moment. We'll see later if we want them.
	if(sizeof($dirlist)>0){
		echo "<div class='subdirs'>";


		foreach ( $dirlist as $subdir ){
			$url	=	relative_path($subdir,$settings['photos_dir']);
			echo 	"<div class='button pink'><a href='?f=$url'>";
			echo 	basename($subdir);
			echo 	"</a></div>";
		}
		echo "</div>\n";
	}
*/
	echo 	"</div>\n";
	return $info;
}

?>
