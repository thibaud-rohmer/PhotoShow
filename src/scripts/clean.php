<?php
/**
 * This file implements the cleaning script
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
 * Clean
 *
 * Your config.php file is read and thumbnails and job get cleaned
 * call the script on the command line or in cron job:
 * > php <path_to_clean.php>
 * The folder from which you call the script does not matter
 *
 * @category  Website
 * @package   Photoshow
 * @license   http://www.gnu.org/licenses/
 */

// Include class files
$toinclude = array( realpath(dirname(__FILE__)."/../classes/HTMLObject.php"),
    realpath(dirname(__FILE__)."/../classes/Page.php"),
    realpath(dirname(__FILE__)."/../classes/Video.php"),
    realpath(dirname(__FILE__)."/../classes/File.php"),
    realpath(dirname(__FILE__)."/../classes/Cleaning.php"),
    realpath(dirname(__FILE__)."/../classes/Settings.php")
);

foreach ( $toinclude as $class_file ){
    if(!include($class_file)){
        throw new Exception("Cannot find ".$class_file." file");
    }
}


// Perform the cleaning
Cleaning::PerformClean();
