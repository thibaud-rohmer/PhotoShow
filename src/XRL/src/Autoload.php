<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

function XRL_autoload($class)
{
    if (strpos($class, ':') !== FALSE)
        throw new Exception('Possible remote execution attempt');

    $class = ltrim($class, '\\');
    if (strncasecmp($class, 'XRL_', 4))
        return FALSE;

    $class = substr($class, 4);
    $class = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class);
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php');
}
spl_autoload_register("XRL_autoload");

