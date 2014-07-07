<?php
/**
 * This file implements the class Guest Token
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhotoShow.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package	  PhotoShow
 * @category  Website
 * @author	  Franck Royer <royer.franck@gmail.com>
 * @author	  Franck Royer <thibaud.rohmer@gmail.com>
 * @copyright 2012 Franck Royer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */

/**
 * Account
 *
 * Implements functions to work with a Guest Token (or key)
 * Read the account from the Guest Token XML file,
 * edit it, and save it.
 * 
 * 
 * @package	  PhotoShow
 * @category  Website
 * @author	  Franck Royer <royer.franck@gmail.com>
 * @author	  Thibaud Rohmer <thibaud.rohmer@gmail.com>
 * @copyright Thibaud Rohmer
 * @license	  http://www.gnu.org/licenses/
 * @link	  http://github.com/thibaud-rohmer/PhotoShow
 */
class GuestToken extends Page
{
    /// Value of the key
    public $key;

    /// Path this key allows access to
    public $path;

    public function __construct(){
    }

    /**
     * Creates a new token in the base
     *
     * @param string $key 
     * @param array  $path 
     * @author Franck Royer
     */ 
    public static function create($path, $key = NULL){

        // A token with no path is useless
        // Only admin can create a token for now
        if(!isset($path) || !CurrentUser::$admin){
            return false;
        }

        if (!isset($key)){
            $key = self::generate_key();
        }

        if (self::exist($key)){
            error_log("ERROR/GuestToken: Key ".$key." already exist, aborting creation");
            return false;
        }

        if(!file_exists(CurrentUser::$tokens_file) || sizeof(self::findAll()) == 0 ){
            // Create file
            $xml	=	new SimpleXMLElement('<tokens></tokens>');
            $xml->asXML(CurrentUser::$tokens_file);
        }

        // I like big keys
        if( strlen($key) < 10){
            return false;
        }

        $token			=	new GuestToken();
        $token->key     =   $key;
        $token->path	=	File::a2r($path);
        $token->save();
        return true;
    }

    /**
     * Save token in the base
     *
     * @return void
     * @author Franck Royer
     */
    public function save(){
        // For now we do not allow an edit on tokens

        $xml		=	simplexml_load_file(CurrentUser::$tokens_file);

        if (self::exist($this->key)){
            //We cannot change an existing key
            return false;
        }

        $xml_token=$xml->addChild('token');
        $xml_token->addChild('key' ,$this->key);
        $xml_token->addChild('path' ,$this->path);

        // Saving into file
        $xml->asXML(CurrentUser::$tokens_file);
    }

    /**
     * Delete a token
     *
     * @param string $key 
     * @return void
     * @author Franck Royer
     */
    public static function delete($key){
        if (!CurrentUser::$admin || !file_exists(CurrentUser::$tokens_file)){
            // Only admin can delete the tokens for now
            return false;
        }

        $xml		=	simplexml_load_file(CurrentUser::$tokens_file);

        $i=0;
        $found = false;
        foreach( $xml as $tk ){
            if((string)$tk->key == $key){
                unset($xml->token[$i]);
                $found = true;
                break;
            }
            $i++;
        }

        if ($found && $xml->asXML(CurrentUser::$tokens_file)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a token already exists
     *
     * @param string $key
     * @return bool
     * @author Franck Royer
     */
    public static function exist($key){

        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        $xml		=	simplexml_load_file(CurrentUser::$tokens_file);

        foreach( $xml as $token ){
            if((string)$token->key == (string)$key)
                return true;
        }

        return false;
    }


    /**
     * Returns an array containing all tokens
     *
     * @return array $tokens, False if not found
     * @author Franck Royer
     */
    public static function findAll(){
        $tokens	=	array();
        
        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        $xml		=	simplexml_load_file(CurrentUser::$tokens_file);

        foreach( $xml as $token ){
            $new_token=array();

            $new_token['key']	= (string)$token->key;
            $new_token['path']	= (string)$token->path;

            $tokens[]=$new_token;
        }
        return $tokens;
    }

    /**
     * Returns an array containing all tokens
     * which has access to the given path
     *
     * @param string $path
     * @return array $tokens, False if not found
     * @author Franck Royer
     */
    public static function find_for_path($path, $exact_path = false){
        $tokens	=	array();
        
        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        foreach( self::findAll() as $token ){
            if ($exact_path){
                if ($token['path'] == $path){
                    $tokens[]=$token;
                }
            } else {
                if (self::view($token['key'], $path)){
                    $tokens[]=$token;
                }
            }
        }
        return $tokens;
    }

    /**
     * Returns the allowed path of a guest token
     *
     * @param string $key 
     * @return path, False if not found
     * @author Franck Royer
     */
    public static function get_path($key){
        $path = "";
        
        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        $xml		=	simplexml_load_file(CurrentUser::$tokens_file);

        foreach( self::findAll() as $token ){
            if((string)$token['key'] == (string)$key){
                $path = $token['path'];
                break;
            }
        }

        return $path;
    }

    /**
     * Returns the url to use a token
     * 
     * @param string $key 
     * @return url, False if not found
     * @author Franck Royer
     */
    public static function get_url($key){
        $url = "";
        
        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        if (self::exist($key)){
            $url = Settings::$site_address."?f=".urlencode(self::get_path($key))."&token=".$key;
        }

        return $url;
    }

    /**
     * Returns true if the token is allowed to view the file
     * in the given path
     *
     * @param string $key
     * @param string $path
     * $return bool
     * @author Franck Royer
     */
    public static function view($key,$path){
        $rpath = File::a2r($path)."/";
        $apath = self::get_path($key)."/";

        // Remove double slashes
        preg_replace('/\/\/+/','/', $rpath);
        preg_replace('/\/\/+/','/', $apath);

         
        // Check if the tokens file exists
        if(!file_exists(CurrentUser::$tokens_file)){
            return false;
        }

        if (!$apath || !$rpath){
            return false;
        }

        if(preg_match("/^".preg_quote($apath, '/')."/", $rpath)){
            return true;
        }
        return false;

    }


    /**
     * Generate a new key
     *
     * @return generated key
     * @author Franck Royer
     */
    public static function generate_key(){
        $key = sha1(uniqid(rand(), true));
        return $key;
    }


    /**
     * Display a list of existing tokens
     * 
     */
    public function toHTML(){
        if (!CurrentUser::$admin){
            // Only admin can see the tokens
            return false;
        }

        echo "<div class='header'>";
        echo "<h1>".Settings::_("token","tokens")."</h1>\n";
        echo "</div>";
        
        // We still want to display the title so the page is not empty
        if ( !file_exists(CurrentUser::$tokens_file)){
            return false;
        }
            echo "<ul>";
            echo "<table>";
            echo "<tbody>";
        foreach(self::findAll() as $t){
            echo "<tr>";
            echo "<td>";
            echo "<form action='?t=Adm&a=DTk' method='post'>\n";
            echo "<input type='hidden' name='tokenkey' value='".$t['key']."' />";
            echo "<input type='submit' class='pure-button button-error' value='".Settings::_("token","deletetoken")."' />";
            echo "</form>";
            echo "</td>";
            echo "<td><a href='".self::get_url($t['key'])."' >".$t['path']."</a></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</ul>";
        echo "</div>";
    }

}

?>
