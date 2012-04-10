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
     * @return pid of the executed command (only linux)
	 * @author Cédric Levasseur/Franck Royer
	 */	
	public function ExecInBackground($cmd) {	
		error_log('DEBUG/Video: Background Execution : '.$cmd,0);
        $pid = 0;
		if (substr(php_uname(), 0, 7) == "Windows"){
		   pclose(popen('start /b '.$cmd.' 2>&1', 'r'));
		} else {
		    exec($cmd . " > /dev/null 2>&1 & echo $!", $output);   
            $pid = intval($output[0]);
		}
        return $pid;
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

        $file_file	       = new File($file);
        $thumb_path_no_ext = Settings::$thumbs_dir.dirname(File::a2r($file))."/".$file_file->name;
        $thumb_path_webm   = $thumb_path_no_ext.'.webm';	
        $thumb_path_jpg    = $thumb_path_no_ext.'.jpg';	


        // Check if thumb folder exist
        if(!file_exists(dirname($thumb_path_webm))){
            @mkdir(dirname($thumb_path_webm),0755,true);
        }

        if (!file_exists($thumb_path_jpg) || filectime($file) > filectime($thumb_path_jpg)) {
            //Create Thumbnail jpg in Thumbs folder
            //TODO: taking 4 seconds within the video won't work for video >4s
            //TODO: scaled thumbnail would be better
            $u=Settings::$ffmpeg_path.' -itsoffset -4  -i "'.$file.'" -vcodec mjpeg -vframes 1 -an -f rawvideo -s 320x240 -y "'.$thumb_path_jpg.'"';
            self::ExecInBackground($u);
        }

        if (self::NoJob($file))// We check that first to allow the clean of old job files
        {
            if (!file_exists($thumb_path_webm) || filectime($file) > filectime($thumb_path_webm)){
                if ($file_file->extension !="webm") {
                    ///Convert video to webm format in Thumbs folder
                    //TODO: Max job limit
                    $u = Settings::$ffmpeg_path.' -threads 4 -i "'.$file.'" '.Settings::$ffmpeg_option.' -y "'.$thumb_path_webm.'"';		
                    $pid = self::ExecInBackground($u);
                    self::CreateJob($file, $pid);
                }
                else {
                    ///Copy original webm video to Thumbs folder
                    copy($file,$thumb_path_webm);
                }
            }
        }
    }

	/**
     * Check if a job is running for the conversion
     * of a video described in the argument
     * Clean existing job files if necessary
	 *   
	 * @return bool if No Job is running for this video
	 * @author Franck Royer
	 */
    public static function NoJob($file) {
        $file_file	       = new File($file);
        $job_filename = Settings::$thumbs_dir.dirname(File::a2r($file))."/".$file_file->name.'.job';

        if (!file_exists($job_filename))
        {
            return true;
        }

        $job_file = fopen($job_filename, "r");

        if (!$job_file)
        {
            error_log('ERROR/Video: Cannot read '.$job_filename.', deleting if possible.');
            unlink($job_filename);
            return true;
        }

        $pid = fgets($job_file);
        fclose($job_file);

        //TODO: windows
        exec('ps ax | grep '.$pid.' | grep -v grep -c', $output);
        if ($pid && $pid != '' && $pid != '0' && intval($output[0]) > 0) {
            // Process is still running
            //error_log('DEBUG/Video: job '.$pid.' is still running for '.$file);
            return false;
        } else { // Process is not running, delete job file
            //error_log('DEBUG/Video: job '.$pid.' is not running, deleting '.$job_filename);
            unlink($job_filename);
            return true;
        }
    }

	/**
     * Create a job file
	 *   
	 * @return void
	 * @author Franck Royer
	 */
    public static function CreateJob($file, $pid) {
        if (!self::NoJob($file)){
            error_log('ERROR/Video: job for '.$file.' already exists, not creating second job file');
            return;
        } 
        if ( !$pid || $pid == '' || $pid == '0'){
            error_log('ERROR/Video: pid for '.$file.' is invalid, not creating job file');
            return;
        }


        // Open file
        $file_file	       = new File($file);
        $job_filename = Settings::$thumbs_dir.dirname(File::a2r($file))."/".$file_file->name.'.job';
        $job_file = fopen($job_filename, "w");

        if (!$job_file) {
            error_log('ERROR/Video: Cannot write on '.$job_filename.'.');
            return;
        }

        //error_log('DEBUG/Video: store PID '.$pid.' in '.$job_filename);
        fwrite($job_file, $pid);
        fclose($job_file);
    }

    //TODO: center the video on y axis
    public function VideoDiv($width='100%',$height='100%',$control=false){
		$c = null;
		self::FastEncodeVideo($this->file);
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
