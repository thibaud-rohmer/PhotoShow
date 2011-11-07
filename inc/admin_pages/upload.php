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
require_once realpath(dirname(__FILE__).'/../../src/accounts.php');
require_once realpath(dirname(__FILE__).'/../../src/settings.php');
require_once realpath(dirname(__FILE__).'/../../src/layout.php');

$settings=get_settings();

// If we aren't logged, or aren't an admin
if(!admin()){
	echo "You aren't supposed to be there.";
	exit();
}


if(isset($_POST['path'])){
	// First, check if this is public.
	if(sizeof($_POST['users'])==0 && sizeof($_POST['groups'])==0){
		$view=who_can_view($_POST['path']);
		
		$allowed_users	=	$view['users'];
		$allowed_groups	=	$view['groups'];

		$public=(sizeof($allowed_groups)==0 && sizeof($allowed_users)==0);
	}else{
		$public=false;
	}

	$dir=realpath($_POST['path']);
	
	// Create a new directory if requested
	if(strlen($_POST['newdir'])>0){
		$newdir=$dir."/".$_POST['newdir'];
		if(!strpos($newdir,'..') && !file_exists($newdir)){
			mkdir($newdir,0750,true);
			mkdir(get_thumb($newdir),0750,true);
			$dir=$newdir;
		}else{
			echo "Error : check the name of the dir";
			return;
		}
		if($public){
			$info['title']="New album:".basename($newdir);
			$info['description']="New Album !";
			$info['link']=$settings['site_url']."?f=".relative_path($dir,$settings['photos_dir']);

			feed("albums",$info);
		}else{
			$info_rights['groups']=$_POST['groups'];
			$info_rights['users']=$_POST['users'];
			edit_rights($dir,$info_rights);
		}
		
	}


	foreach ($_FILES["images"]["error"] as $key => $error) {
		// Check that is uploaded
	    if ($error == UPLOAD_ERR_OK) {
			// Name of the stored file
	        $tmp_name = $_FILES["images"]["tmp_name"][$key];
	
			// Name on the website
	        $name = $_FILES["images"]["name"][$key];
			
			$info = pathinfo($name);
			$base_name =  basename($name,'.'.$info['extension']);
			
			// Rename until this name isn't taken
			$i=1;
			while(file_exists("$dir/$name")){
				$name=$base_name."-".$i.".".$info['extension'];
				$i++;
			}
			
			// Save the files
	        move_uploaded_file($tmp_name, "$dir/$name");
			
			if(!$public && strlen($_POST['newdir'])<0){
				$info_rights['groups']=$_POST['groups'];
				$info_rights['users']=$_POST['users'];
				edit_rights("$dir/$name",$info_rights);
			}
	    }
	}
}

$options="<option value='".$settings['photos_dir']."'>.</option>";
foreach(list_dirs($settings['photos_dir'],true) as $dir){
	$options .= "<option value='$dir'>".basename($dir)."</option>\n";
	foreach(list_dirs($dir,true) as $subdir){
		$options .= "<option value='$subdir'>--".basename($subdir)."</option>\n";
	}
}
?>


<div class="admin_box">
	<div class="admin_box_title">Upload</div>
	<div class="admin_box_content">
		<form action="#" method="post" enctype="multipart/form-data" class='niceform'>
		<table class='admin_table'>
			<tr><td>File</td><td><input name="images[]" type="file" multiple /></td></tr>
			<tr><td>Path</td><td><select name="path"><?php echo $options; ?></select>
			<tr><td>New Directory</td><td><input name="newdir" type="text" /></td></tr>
			<?php 
			
			echo $url;
				echo "<tr><td>Private - Group :</td><td>";
				foreach(get_groups() as $g)
					echo "<label><input name='groups[]' type='checkbox' value='$g'> $g </label>";
				echo "</td></tr><tr><td>Private - User :</td><td>";
				foreach(get_logins() as $u)
					echo "<label><input name='users[]' type='checkbox' value='$u'> $u </label>"; 
				echo "</td></tr>";
			?>
			<tr><td colspan='2'><input type="submit" value="Send" class='button blue'/></td></tr>
		</table>
		</form>
	</div>
</div>