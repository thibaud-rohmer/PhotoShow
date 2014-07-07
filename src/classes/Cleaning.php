<?php
/**
 * This file implements the class Settings.
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
 * @author    Franck Royer <royer.franck@gmail.com>
 * @copyright 2012 Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Cleaning 
 *
 * Clean any useless files (old job, old thumbs) from thumbs_dir
 *
 * @category  Website
 * @package   Photoshow
 * @author    Franck Royer <royer.franck@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

class Cleaning
{

    /**
     * Constructor
     */
    public function __construct(){
    }

    /**
     * Perform the cleaning
     *
     * @return void
     * @author Franck Royer
     */
    static public function PerformClean(){
        if (!isset(Settings::$photos_dir) || !isset(Settings::$thumbs_dir)) {
            Settings::init();
        }

        if ( !is_dir(Settings::$photos_dir) || !is_dir(Settings::$thumbs_dir) ) {
            error_log("ERROR/Cleaning::PerformClean: ".$photos_dir." or ".$thumbs_dir." do not exit, cannot proceed.");
            return;
        }

        //TODO Windows
        // Clean Old thumbnails
        exec("cd ".Settings::$thumbs_dir." && find . -type f -iregex '.*[^\(xml\)\(job\)]$' 2>&1", $output, $ret_var);

        if ($ret_var != 0) {
            error_log("ERROR/Cleaning::PerformClean: Execution failed: ".print_r($output));
        }

        foreach ($output as $file) {
            if (!file_exists(Settings::$photos_dir.'/'.$file) ){
                error_log('DEBUG/Cleaning::PerformClean: erase '.Settings::$thumbs_dir.'/'.$file."\n");
                unlink(Settings::$thumbs_dir.'/'.$file);
            }
        }

        // Now cleaning Pending jobs
        unset($output); unset($ret_var);
        exec("cd ".Settings::$thumbs_dir." && find . -type f -name '*job' 2>&1", $output, $ret_var);
        if ($ret_var != 0) {
            error_log("ERROR/Cleaning::PerformClean: Execution failed: ".print_r($output));
            return;
        }

        foreach ($output as $jobfilename) {
            // need the absolute path of the video file
            //error_log('DEBUG/Cleaning::PerformClean: processing job '.$jobfilename."\n");
            $jobfile = new File(Settings::$thumbs_dir.'/'.$jobfilename);
            $filename_no_ext = Settings::$photos_dir.'/'.dirname($jobfilename).'/'.$jobfile->name;
            $files = glob($filename_no_ext.'.*');

            if (empty($files)){
                error_log("ERROR/Cleaning::PerformClean: no file found for job ".$jobfilename.", deleting\n");
                unlink(Settings::$thumbs_dir.'/'.$jobfilename);
                continue;
            }

            //error_log('DEBUG/Cleaning::PerformClean: calling NoJob with '.$files[0]."\n");
            Video::NoJob($files[0]);
        }

        // Finally clean empty files
        unset($output); unset($ret_var);
        exec("cd ".Settings::$thumbs_dir." && find . -size 0b 2>&1", $output, $ret_var);
        if ($ret_var != 0) {
            error_log("ERROR/Cleaning::PerformClean: Execution failed: ".print_r($output));
            return;
        }
        foreach ($output as $emptyfile) {
            unlink(Settings::$thumbs_dir.'/'.$emptyfile);
        }
    }
}
?>
