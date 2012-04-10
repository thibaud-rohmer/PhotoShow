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
		if(!isset($file) || !File::Type($file) || File::Type($file) != "Video") {
			return;
        }

        
		/// File object
		$this->file = $file;
		
		/// Set relative path (url encoded)
		$this->fileweb	=	urlencode(File::a2r($file));
		
	}
	
	/**
	 * Create Asynchrone Execution (compatibles Linux/Windows)
	 *
	 * @param string $file 
	 * @author Cédric Levasseur
	 */	
	public function execInBackground($cmd) {	
		error_log('Background Execution : '.$cmd,0);
		if (substr(php_uname(), 0, 7) == "Windows"){
		   pclose(popen('start /b '.$cmd.' 2>&1', 'r'));
		} else {
		    exec($cmd . " > /dev/null &");   
		}
	} 
	
	
	/**
	 * Asynchronous Convert all Video format to video/webm
	 *   
	 * Use ffmpeg for conversion
	 * @return void
	 * @author Cédric Levasseur
	 */
    public static function FastEncodeVideo($file) {

        /// Check item
        if(!File::Type($file) || File::Type($file) != "Video"){
            return;
        }

        $basefile	     = new File($file);
        $basepath	     = File::a2r($file);	
        $thumb_path_webm = Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.'.webm';	
        $thumb_path_jpg	 = Settings::$thumbs_dir.dirname($basepath)."/".$basefile->name.'.jpg';	


        // Check if thumb folder exist
        if(!file_exists(dirname($thumb_path_webm))){
            @mkdir(dirname($thumb_path_webm),0755,true);
        }

        if (!file_exists($thumb_path_jpg) || filectime($file) > filectime($thumb_path_jpg)) {
            //Create Thumbnail jpg in Thumbs folder
            //TODO: taking 4 seconds within the video won't work for video >4s
            $u=Settings::$ffmpeg_path.' -itsoffset -4  -i "'.$file.'" -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y "'.$thumb_path_jpg.'"';
            self::execInBackground($u);
        }

        if (!file_exists($thumb_path_webm) || filectime($file) > filectime($thumb_path_webm)){
            if ($basefile->extension !="webm") {
                ///Convert video to webm format in Thumbs folder
                $u=Settings::$ffmpeg_path.' -threads 4 -i "'.$file.'" '.Settings::$ffmpeg_option.' -y "'.$thumb_path_webm.'"';		
                self::execInBackground($u);
            }
            else {
                ///Copy original webm video to Thumbs folder
                copy($file,$thumb_path_webm);
            }
        }
    }

    public function VideoDiv($width='100%',$height='100%',$control=false){
		$c = null;
		Video::FastEncodeVideo($this->file);
		$wh = ' height="'.$height.'" width="'.$width.'"';
		if ($control) { $c = ' controls="controls"';}
		echo '<video'.$wh.$c.'><source src="?t=Vid&f='.$this->fileweb.'" type="video/webm" /></video>';
		//echo 'Webm Video Codec not found.Plaese up to date the brower or Download the codec <a href="http://tools.google.com/dlpage/webmmf">Download</a>';
	}	
	
	/**
	 * Display the video on the website
	 *
	 * @return void
	 * @author Cédric Levasseur
	 */
	public function toHTML(){	
        self::VideoDiv('',400,true);
	}

}

?>
