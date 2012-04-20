<?php
/**
 * This file implements unit tests for GuestToken class
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
 * Unit test Guest Token
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
class GuestTokenTest extends TestUnit
{

    /**
     * Test if keys are generated
     * @test
     * @author Franck Royer
     */
    public function test_generate_key()
    {
        $key = GuestToken::generate_key();
        $this->assertGreaterThan(10, strlen($key));
    }

    /**
     * Quick test on the randomness of the keys
     * @test
     * @depends test_generate_key
     */
    public function test_generate_key_random()
    {
        $key1 = GuestToken::generate_key();
        $key2 = GuestToken::generate_key();
        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test the create feature
     * @test
     * @depends test_generate_key
     */
    public function test_create(){
        // From scratch
        self::delete_tokens_file();
        self::login_as_admin();
       
        $folder1 = Settings::$photos_dir."tokenfolder";
        $ret = GuestToken::create($folder1);
        $this->assertTrue($ret);

        $tokens = GuestToken::findAll();
        $this->assertCount(1, $tokens);
        $this->assertArrayHasKey('key', $tokens[0]);
        $this->assertArrayHasKey('path', $tokens[0]);
        $this->assertEquals(File::a2r($folder1), $tokens[0]['path']);
        $this->assertRegexp('/.{10}.*/',$tokens[0]['key']);

        // we shouldn't create key for non-existing folders
        try {
            $folder2 = Settings::$photos_dir."tokenfolder2";
            if (file_exists($folder2)){
                rmdir($folder2);
            }
            $ret = GuestToken::create($folder2);
        } catch(Exception $e) {
            $this->assertCount(1, GuestToken::findAll());

            mkdir($folder2);
            $ret = GuestToken::create($folder2);
            $this->assertTrue($ret);
            $this->assertCount(2, GuestToken::findAll());

            $this->assertCount(1, GuestToken::find_for_path($folder2));

            $tokens2 = GuestToken::find_for_path($folder2);

            $this->assertEquals(File::a2r($folder2), $tokens2[0]['path']); 

            $ret = GuestToken::exist($tokens[0]['key']);
            $this->assertTrue($ret);

            $ret = GuestToken::delete($tokens[0]['key']);
            $this->assertTrue($ret);
            $this->assertCount(1, GuestToken::findAll());

            return;
        }
        $this->fail('Token has been creating on an inexisting folder');
    }

    /**
     * test exists function
     * @test
     * @depends test_create
     */
    public function test_exist(){
        self::login_as_admin();
        self::create_token();

        $tokens = GuestToken::findAll();
        $key = $tokens[0]['key'];

        $this->assertTrue(GuestToken::exist($key));
    }


    /**
     * test get_path function
     * @test
     * @depends test_create
     * @depends test_generate_key
     */
    public function test_get_path(){
        self::login_as_admin();
        $key = Guesttoken::generate_key();
        $path = Settings::$photos_dir."/tokenfolder";

        GuestToken::create($path,$key);

        $tpath = GuestToken::get_path($key);
        $this->assertEquals(File::a2r($path), $tpath);

    }

    /**
     * test view function
     * @test
     * @depends test_generate_key
     */
    public function test_view(){
        //prepare
        self::login_as_admin();
        self::delete_tokens_file();
        $paths = array();
        $keys = array();
        $paths[1] = Settings::$photos_dir."/tokenfolder";
        $paths[2] = Settings::$photos_dir."/tokenfolder2";
        $paths[3] = Settings::$photos_dir."/tokenfolder/subfolder";

        for ($i = 1; $i <= 3; $i++){
            if (!file_exists($paths[$i])){
                mkdir($paths[$i]);
            }

           $keys[$i] = Guesttoken::generate_key();
            GuestToken::create($paths[$i],$keys[$i]);
        }
        CurrentUser::logout();

        //test
        for ($i = 1; $i <= 3; $i++){
            $this->assertTrue(GuestToken::view($keys[$i], $paths[$i]));
        }

        $this->assertFalse(GuestToken::view($keys[3], $paths[1]));
        $this->assertFalse(GuestToken::view($keys[3], $paths[2]));
        $this->assertFalse(GuestToken::view($keys[2], $paths[1]));
        $this->assertTrue(GuestToken::view($keys[1], $paths[3]));

    }

    /**
     * test delete
     * @test
     * @depends test_create
     * @depends test_generate_key
     * @depends test_exist
     */
    public function test_delete(){
        self::login_as_admin();
        self::delete_tokens_file();
        $key = Guesttoken::generate_key();
        $path = Settings::$photos_dir."/tokenfolder";
        $key2 = Guesttoken::generate_key();
        $path2 = Settings::$photos_dir."/subfolder";
        GuestToken::create($path,$key);
        GuestToken::create($path2,$key2);

        $this->assertFalse(GuestToken::delete(GuestToken::generate_key()));
        $this->assertCount(2, GuestToken::findAll());
        $this->assertTrue(GuestToken::delete($key));
        $this->assertCount(1, GuestToken::findAll());
        $this->assertFalse(GuestToken::exist($key));
        $this->assertTrue(GuestToken::exist($key2));
    }

    /**
     * Verify toHTML gives an output
     * @test
     * @depends test_create
     */
    public function test_toHTML()
    {
        self::login_as_admin();
        self::create_token();

        $this->expectOutputRegex("/..*/");
        $guest_token = new GuestToken();
        $guest_token->toHTML();
    }

    /**
     * Verify toHTML gives an output when there is not token file
     * @test
     */
    public function test_toHTML_no_tokens_file()
    {
        self::login_as_admin();
        self::delete_tokens_file();

        $this->expectOutputString("");
        $guest_token = new GuestToken();
        $guest_token->toHTML();
    }

}
?>
