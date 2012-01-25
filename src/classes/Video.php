<?php
/**
 * This file implements the class Image.
 * 
 * PHP versions 4 and 5
 */

/**
 * Image
 *
 * The Video is displayed in the ImagePanel. This file
 * implements its displaying.
 * Video File : only ogv (free licence)
 *
 * @category  Website
 * @package   Photoshow
 * @license   http://www.gnu.org/licenses/
 */

class Video implements HTMLObject
{
	/// URLencoded version of the relative path to file
	static public $fileweb;
	
	/// URLencoded version of the relative path to directory containing file
	private $dir;
	
	/// Width of the image
	private $x;
	
	/// Height of the image
	private $y;

	/// Force big image or not
	private $t;
	
	/**
	 * Create Video
	 *
	 * @param string $file 
	 * @author Cédric Levasseur
	 */
	public function __construct($file=NULL,$forcebig = false){
	
		if( !Judge::view($file)){
			return;
		}
		
		/// Check file type
		if(!isset($file) || !File::Type($file) || File::Type($file) != "Video")
			return;

		/// File object
		$this->file = $file;
	}
	
	/**
	 * Asyncrhonous Convert all Video format to video/webm
	 *   
	 * Use ffmpeg for conversion
	 * @return void
	 * @author Cédric Levasseur
	 */
	public static function FastEncodeVideo($file) {
	
		$basefile	= 	new File($file);
		$basepath	=	File::a2r($file);	
		$path_thumb_webm	=	File::Root().'/'.Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.'.webm';	
		$path_thumb_jpg	=	File::Root().'/'.Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.'.jpg';	
		
		if(!file_exists($path_thumb_webm) || filectime($file) > filectime($path_thumb_webm)  ){
			/// Create Folder
			if(!file_exists(dirname($path_thumb_webm))){
				@mkdir(dirname($path_thumb_webm),0755,true);
			}
		}

		 error_log($file,0);
		 error_log($path_thumb_webm,0);
		
		if ($basefile->extension !="webm") {
			if (!file_exists($path_thumb_webm)){
				///Create Thumbnail jpg in  Thumbs folder
				$u=Settings::$ffmpeg_path.' -itsoffset -4  -i '.$file.' -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y '.$path_thumb_jpg;
				error_log($u,0);				
				pclose(popen('start /b '.$u.'', 'r'));
				///Convert video to webm format in Thumbs folder
				$u=Settings::$ffmpeg_path.' -threads 4 -i '.$file.' '.Settings::$ffmpeg_option.' -y '.$path_thumb_webm.' 2>&1';		
				error_log($u,0);
				pclose(popen('start /b '.$u.'', 'r'));
			}
		} else {
			//Create Thumbnail jpg in Thumbs folder
			$u=Settings::$ffmpeg_path.' -itsoffset -4  -i '.$file.' -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y '.$path_thumb_jpg;
			pclose(popen('start /b '.$u.'', 'r'));
			///Copy original webm video to Thumbs folder
			copy($file,$path_thumb_webm);
		}
	}
	
	public static function Video($file,$width='100%',$height='100%',$control=false){
	
		if( !Judge::view($file)){
			return;
		}
		if(function_exists("error_reporting")){
			error_reporting(0);
		}

		/// Check item
		if(!File::Type($file) || File::Type($file) != "Video"){
			return;
		}
		
		$basefile	= 	new File($file);
		$basepath	=	File::a2r($file);	
		$path_webm	=	Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.'.webm';			

		$c = null;
		Video::FastEncodeVideo($file);
		$wh = ' height="'.$height.'" width="'.$width.'"';
		if ($control) { $c = ' controls="controls"';}
		echo '<video'.$wh.$c.'><source src="'.$path_webm.'" type="video/webm" /></video>';
		//echo 'Webm Video Codec not found.Plaese up to date the brower or Download the codec <a href="http://tools.google.com/dlpage/webmmf">Download</a>';
	}	
	
	/**
	 * Display the video on the website
	 *
	 * @return void
	 * @author Cédric Levasseur
	 */
	public function toHTML(){	
	self::Video($this->file,'',400,true);
	}

}

?>