<?php
/**
 * This file implements the class Page.
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
 * Page
 *
 * The page holds all of the data. This class build the entire
 * structure of the website, as it is viewed by the user.
 *
 * @category  Website
 * @package   Photoshow
 * @author    Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license   http://www.gnu.org/licenses/
 * @link      http://github.com/thibaud-rohmer/PhotoShow
 */

abstract class Page implements HTMLObject
{
		/**
		 * Generate an insanely beautiful header.
		 * TODO: Title
		 *
		 * @return void
		 * @author Thibaud Rohmer
		 */
		public function header($head_content=NULL){
			echo "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>\n";
			echo "<html>";
			echo "<head>\n";
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\n";
			echo "<meta name='viewport' content='width=device-width, initial-scale=1'>\n";
			echo "<title>".Settings::$name."</title>\n";
			echo "<meta name='author' content='Thibaud Rohmer'>\n";
			echo "<link rel='icon' type='image/ico' href='inc/favico.ico'>";

			/// CSS
			echo "<link rel='stylesheet' href='inc/stylesheets/pure-min.css'>\n";
			echo "<link rel='stylesheet' href='inc/stylesheets/pure-grid.css'>\n";
			echo "<link rel='stylesheet' href='inc/stylesheets/font-awesome.min.css'>\n";
			
			echo "<link rel='stylesheet' href='src/stylesheets/structure.css' type='text/css' media='screen' charset='utf-8'>\n";
			echo "<link rel='stylesheet' href='src/stylesheets/buttons.css' type='text/css' media='screen' charset='utf-8'>\n";
			echo "<link rel='stylesheet' href='src/stylesheets/theme.css' type='text/css' media='screen' charset='utf-8'>\n";
			echo "<link rel='stylesheet' href='src/stylesheets/side-menu.css' type='text/css' media='screen' charset='utf-8'>\n";

			echo "<link rel='stylesheet' href='user/themes/".Settings::$user_theme."/style.css' type='text/css' media='screen' charset='utf-8'>\n";

			/// Trick to hide "only-script" parts
	 		echo "<noscript><style>.noscript_hidden { display: none; }</style></noscript>";

			/// JS
			echo "<script src='inc/jquery.js'></script>\n";
			echo "<script src='inc/jquery-ui.js'></script>\n";
			echo "<script src='inc/mousewheel.js'></script>\n";
			echo "<script src='inc/jquery.scrollTo.js'></script>\n";
			echo "<script src='inc/jquery.fileupload.js'></script>\n";
			echo "<script src='inc/js/photosphere/three.min.js'></script>\n";
			echo "<script src='inc/js/photosphere/sphere.js'></script>\n";


			echo "<script src='src/js/menu.js'></script>\n";
			echo "<script src='src/js/panel.js'></script>\n";
			echo "<script src='src/js/slideshow.js'></script>\n";
			echo "<script src='src/js/image_panel.js'></script>\n";
			echo "<script src='src/js/keyboard.js'></script>\n";



			if(CurrentUser::$admin || CurrentUser::$uploader){
				echo "<link rel='stylesheet' href='inc/fileupload-ui.css' type='text/css' media='screen' charset='utf-8'>\n";
				echo "<script src='inc/jquery.fileupload-ui.js'></script>\n";
				echo "<script src='src/js/admin.js'></script>\n";
			}

			// Add specific head content if needed
			if ($head_content)
			{
				echo $head_content;
			}

			echo "</head>";

		}
}
?>
