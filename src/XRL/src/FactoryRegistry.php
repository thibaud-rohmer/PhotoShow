<?php
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

abstract class  XRL_FactoryRegistry
implements      ArrayAccess
{
    protected $_interfaces;

    public function offsetSet($interface, $obj)
    {
        if (!is_string($interface))
            ; /// @TODO

        if (!is_object($obj))
            ; /// @TODO

        $interface = strtolower($interface);
        if (!isset($this->_interfaces[$interface]))
            ; /// @TODO

        if (!($obj instanceof $interface))
            ; /// @TODO

        $this->_interfaces[$interface] = $obj;
    }

    public function offsetGet($interface)
    {
        if (!is_string($interface))
            ; /// @TODO

        $interface = strtolower($interface);
        return $this->_interfaces[$interface];
    }

    public function offsetExists($interface)
    {
        if (!is_string($interface))
            return FALSE;

        $interface = strtolower($interface);
        return isset($this->_interfaces[$interface]);
    }

    public function offsetUnset($interface)
    {
        /// @TODO
    }
}

