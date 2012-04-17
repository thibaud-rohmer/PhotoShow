<?php
/**
 * This file implements tools for unit tests
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
 * Unit test tools
 *
 * I used that for some debug. It's incomplete and I guess
 * It would be better to have a proper framework for unit 
 * test on PHP website. Anyway, it does not harm anyone for now
 *
 * @category  Website
 * @package   Photoshow
 * @license   http://www.gnu.org/licenses/
 */

class UnitTest extends PHPUnit_Framework_TestCase
{

    /**
     * constructor
     */
    function __construct(){
        parent::__construct();
        set_include_path(realpath(dirname(__FILE__)."/../classes/"));
    }

    /**
     * To login as admin for tests needing it
     * 
     */
    public function login_as_admin(){
        //Make Current User admin
    }

    /**
     * Functions used to setup the environment for other tests
     *
     */
    public function init_config(){
        //Somehow setup a config.ini and init
        //some temporary folders that can be used for testing
    }

}
?>
