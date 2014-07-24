<?php
/**
 * This file contains the website configuration.
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

// Folder where your pictures are stored.
// Must be at least readable by web server process
$config->photos_dir   = "path_to_your_photos_dir_goes_here";

// Folder where PhotoShow parameters and thumbnails are stored.
// Must be writable by web server process
$config->ps_generated   = "path_where_photoshow_generates_files_goes_here";

// Local timezone. Default one is "Europe/Paris".
#$config->timezone = "Europe/Paris";

// ImageMagick (convert tool) path.
#$config->imagemagick_path = '/usr/local/bin/convert';

// Quality for small pictures, scala: 0-100
#$config->quality_small = 90;

/// Quality of mini thumbnails in overview, scala: 0-100
#$config->quality_mini = 90;
