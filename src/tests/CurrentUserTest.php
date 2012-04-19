<?php
/**
 * This file implements unit tests for Current class
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
 * Unit test Guest Toke
 *
 * I used that for some debug. It's incomplete and I guess
 * It would be better to have a proper framework for unit 
 * test on PHP website. Anyway, it does not harm anyone for now
 *
 * @category  Website
 * @package   Photoshow
 * @license   http://www.gnu.org/licenses/
 */

require_once(realpath(dirname(__FILE__)."/TestUnit.php"));
class CurrentUserTest extends TestUnit
{

    /**
     * Test login
     * @test
     */
    public function test_login(){
		session_unset();
        CurrentUser::logout();
        self::login_as_user();

        $this->assertEquals("testuser", $_SESSION['login']);
        $this->assertNull($_SESSION['token']);
        $this->assertNotNull(CurrentUser::$account);
        $this->assertFalse(CurrentUser::$admin);
    }

    /**
     * Test logout
     * @test
     * @depends test_login
     */
    public function test_logout(){
		session_unset();
        self::login_as_user();
        CurrentUser::logout();

        //TODO: I guess we need to read the doc to use phpunit, _SESSION and session_unset()
        $this->assertNull($_SESSION['login']);
        $this->assertNull(CurrentUser::$account);
        $this->assertNull($_SESSION['token']);
        $this->assertFalse(CurrentUser::$admin);
    }


}
?>
