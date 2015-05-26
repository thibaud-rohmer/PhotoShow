<?php
/**
 * This file implements the class Admin.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE:
 * 
 * This file is part of PhotoShow.
 *
 * PhotoShow is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhotoShow is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright 2011 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Admin
 *
 * Aministration panel
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */
 class Admin extends Page
 {
 	/// Admin page
 	public $page;

 	/// Menu of the Admin page
 	public $menu;

 	/// Admin action
 	static public $action = "stats";

 	/**
 	 * Create admin page
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function __construct(){

 		/// Check that current user is an admin or an uploader
	 	if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
	 		return;
	 	}


 		/// Get actions available for Uploaders too
 		if(isset($_GET['a'])){
	 		switch($_GET['a']){
	 			case "Abo" 		: 	$this->page = new AdminAbout();
	 								break;
	 								
		 		case "Upl"		:	if(isset($_POST['path'])){
		 								AdminUpload::upload();
		 								CurrentUser::$path = File::r2a(stripslashes($_POST['path']));
		 							}
		 							break;
				
				case "Mov"		:	if(isset($_POST['pathFrom'])){
										try{
	 										CurrentUser::$path = File::r2a(dirname(stripslashes($_POST['pathFrom'])));	
										}catch(Exception $e){
											CurrentUser::$path = Settings::$photos_dir;
										}
									}
	 								Admin::move();
	 								
	 								if(isset($_POST['move']) && $_POST['move']=="rename"){
										try{
											if(is_dir(File::r2a(stripslashes($_POST['pathFrom'])))){
	 											CurrentUser::$path = dirname(File::r2a(stripslashes($_POST['pathFrom'])))."/".stripslashes($_POST['pathTo']);	
	 										}
										}catch(Exception $e){
											CurrentUser::$path = Settings::$photos_dir;
										}
									}
		 							
									break;

				case "Del"		:	if(isset($_POST['del'])){
										if(!is_array($_POST['del'])){
			 								CurrentUser::$path = dirname(File::r2a(stripslashes($_POST['del'])));
										}else{
			 								CurrentUser::$path = dirname(File::r2a(stripslashes($_POST['del'][0])));
										}
		 								Admin::delete();
		 							}
									break;
	 		}
	 	}

 		/// Check that current user is an admin
	 	if( !(CurrentUser::$admin) ){
	 		return;
	 	}

 		/// Get action
 		if(isset($_GET['a'])){
	 		switch($_GET['a']){
		 		case "Sta"		:	$this->page = new AdminStats();
		 							break;

		 		case "VTk"		:	$this->page = new GuestToken();
		 							break;

				case "DTk" 		:	if(isset($_POST['tokenkey'])){
										GuestToken::delete($_POST['tokenkey']);
									}
									$this->page = new GuestToken();
									break;

		 		case "Acc"		:	if(isset($_POST['edit'])){
										Account::edit($_POST['login'],$_POST['old_password'],$_POST['password'],$_POST['name'],$_POST['email'],NULL,$_POST['language']);
									}
									if(isset($_POST['login'])){
										$this->page = new Account($_POST['login']);
									}else{
										$this->page = CurrentUser::$account;
									}

									break;

				case "GC"		:	Group::create($_POST['group']);
									$this->page = new Group();
									break;

				case "AAc"		:	Account::create($_POST['login'],$_POST['password'],$_POST['verif']);
									$this->page = new Group();
									break;
				
				case "AGA"		:	$a = new Account($_POST['acc']);
									$a->add_group($_POST['group']);
									$a->save();
									$this->page = CurrentUser::$account;
									break;

				case "AGR"		:	$a = new Account($_POST['acc']);
									$a->remove_group($_POST['group']);
									$a->save();
									$this->page = CurrentUser::$account;
									break;

				case "ADe"		:	Account::delete($_POST['name']);
									$this->page = new Group();
									break;

				case "GEd"		:	Group::edit($_POST);
									$this->page = new Group();
									break;

				case "GDe"		:	Group::delete($_GET['g']);
									$this->page = new Group();
									break;

				case "CDe"		:	CurrentUser::$path = File::r2a($_POST['image']);
									Comments::delete($_POST['id']);
									$this->page = new MainPage();
									break;

				case "JS"		:	break;

				case "EdA"		:	$this->page = new Group();
									break;
				
				case "GAl"		:	if(isset($_POST['path'])){
										Settings::gener_all(File::r2a(stripslashes($_POST['path'])));
									}
				case "Set" 		:	if(isset($_POST['name'])){
										Settings::set();
									}
									$this->page = new Settings();
									break;
		 		}
		}
		
		if(!isset($this->page)){
			$this->page = new AdminAbout();			
		}

	 	/// Create menu
	 	$this->menu = new AdminMenu();

	}

 	/**
 	 * Move files on the server
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public static function move(){

 		/// Just to be really sure... 
 		if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
 			return;
 		}

 		$from 	= File::r2a(stripslashes($_POST['pathFrom']));
 		$to 	= dirname($from)."/".stripslashes($_POST['pathTo']);
 		$type 	= $_POST['move'];

 		if($from == $to){
 			return;
 		}

		if(file_exists($to)){
			/// We don't want to overwrite existing data
			return;
		}

 		if($type == "rename"){
			/// Metadatas need to be done first: once moved/deleted,
			/// we won't be able to compute from the original file
			Admin::manage_metadatas(stripslashes($_POST['pathFrom']), stripslashes($_POST['pathTo']));
 			@rename($from,$to);
 			return;
 		}

 		/// We are moving multiple files
 		$files = scandir($from);
 		foreach($files as $file){
 			if($file != "." && $file!=".."){
				Admin::manage_metadatas(stripslashes($_POST['pathFrom'])."/".$file,
							stripslashes($_POST['pathTo'])."/".$file);
	 			@rename($from."/".$file,$to."/".$file);
	 		}
 		}

 		return;
	}


 	/**
 	 * Delete files on the server
 	 * 
 	 * @author Thibaud Rohmer
 	 */
 	public function delete(){

 		/// Just to be really sure... 
 		if( !(CurrentUser::$admin || CurrentUser::$uploader) ){
 			return;
 		}

 		if(!is_array($_POST['del'])){
			$rela_del = stripslashes($_POST['del']);
			$del 	=	File::r2a($rela_del);
			Admin::manage_metadatas($rela_del);
			Admin::rec_del($del);
 		}else{
 			foreach($_POST['del'] as $todel){
				$rela_del = stripslashes($todel);
				$del 	=	File::r2a($rela_del);
				Admin::manage_metadatas($rela_del);
				Admin::rec_del($del);
 			}
 		}
	}

	/**
	 * Reccursively delete all files in $dir
	 * 
	 * @param string $dir
	 * @author Thibaud Rohmer
	 */
	public function rec_del($dir){
 		if($dir == Settings::$photos_dir){
 			return;
 		}

		if(is_file($dir)){
			return unlink($dir);
		}

		$dirs 	=	Menu::list_dirs($dir);
		$files 	= 	Menu::list_files($dir,false,true,false,true);

		foreach($dirs as $d){
			Admin::rec_del($d);
		}
		
		foreach($files as $f){
			unlink($f);
		}

		return rmdir($dir);
	}

	/**
	 * Manage all metadatas (tokens, _*.xml, thumbnails)
	 * associated to a file or folder
	 *
	 * @param string $file
	 * @param string $to optional parameter for a rename
	 */
	public function manage_metadatas($file, $to = null){
		$originalFile = File::r2a($file);
		$thumbFile = Settings::$thumbs_dir.$file;
		$thumbFile_dirname = dirname($thumbFile);
		$isVideo = File::Type($originalFile) == "Video";

		/// Tokens
		/// It doesn't make any sense to rename a token: its link might
		/// have been already shared, which is now broken because of the
		/// file deletion/renaming, so delete them in all case
		GuestToken::delete_file_tokens($file);

		/// XML
		if(is_file($originalFile)){
			/// XML of folders are inside them, so they will be moved/deleted with the thumbnails
			$xml_metadatas = array("comments","rights");
			foreach($xml_metadatas as $xml_metadata){
				$xml_metadata = "_".$xml_metadata.".xml";
				$xml_file = $thumbFile_dirname."/.".mb_basename($file).$xml_metadata;
				if (file_exists($xml_file)){
					if (isset($to))
						rename($xml_file, $thumbFile_dirname."/.".mb_basename($to).$xml_metadata);
					else
						unlink($xml_file);
				}
			}
		}

		/// Thumbnails
		$originalFile_obj = new File($originalFile);
		if ($isVideo){
			/// A thumbnail of a picture has the same filename of the original file, but it's a jpg for a video
			$thumbFile = $thumbFile_dirname."/".$originalFile_obj->name.".jpg";
			$thumbFileTo = $thumbFile_dirname."/".mb_basename($to, $originalFile_obj->extension)."jpg";
			$webFile = $thumbFile_dirname."/".$originalFile_obj->name.".webm";
			$webFileTo = $thumbFile_dirname."/".mb_basename($to, $originalFile_obj->extension)."webm";
		}
		else{
			$thumbFileTo = $thumbFile_dirname."/".$to;
			$webFile = $thumbFile_dirname."/".$originalFile_obj->name."_small.".$originalFile_obj->extension;
			$webFileTo = $thumbFile_dirname."/".mb_basename($to, '.'.$originalFile_obj->extension)."_small.".$originalFile_obj->extension;
		}

		if (!file_exists($thumbFile))
			return;
		if (isset($to)){
			rename($thumbFile, $thumbFileTo);
			// The webfile might not have been created yet, or $file is < 1200px, or $file is a folder:
			if (file_exists($webFile))
				rename($webFile, $webFileTo);
		}
		else{
			Admin::rec_del($thumbFile);
			if (file_exists($webFile))
				// Only files can have a webFile, so we don't need rec_del()
				unlink($webFile);
		}
	}

	 /**
	  * Display admin page
	  * 
	  * @author Thibaud Rohmer
	  */
	public function toHTML(){
		$this->header();
		echo "<body>";

		echo "<div id='layout'>\n";	

		echo "<a href='#menu' id='menuLink' class='menu-link'><span></span></a>";
		echo "<div id='menu'><div class='pure-menu menu pure-menu-open'>\n";
		
 		$this->menu->toHTML();
 		echo "</div>\n";
	 	echo "</div>";

		if(isset($_GET['a']) && $_GET['a']=="JS"){
			$this->page = new JS();
		}else{
		 	$this->page->toHTML();			
		}

	 	echo "</div>";
		echo "<script src='inc/ui.js'></script>\n";
 
	}

 }

 ?>
