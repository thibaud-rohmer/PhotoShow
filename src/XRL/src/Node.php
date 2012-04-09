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

class XRL_Node
{
    protected $_properties;

    public function __construct(XMLReader $reader, $validate)
    {
        $skipNodes = array(XMLReader::SIGNIFICANT_WHITESPACE);
        do {
            if (!$reader->read()) {
                throw new InvalidArgumentException(
                    'Unexpected end of document'
                );
            }
            if ($validate && !$reader->isValid())
                throw new InvalidArgumentException('Invalid document');
        } while (in_array($reader->nodeType, $skipNodes));

        $fields = array(
            'name',
            'nodeType',
            'value',
            'isEmptyElement',
        );

        $this->_properties = array();
        foreach ($fields as $field)
            $this->_properties[$field] = $reader->$field;
    }

    public function __get($field)
    {
        if (!isset($this->_properties[$field]))
            throw new UnexpectedValueException("Unknown property '$field'");

        return $this->_properties[$field];
    }

    public function emptyNodeExpansionWorked()
    {
        if ($this->_properties['nodeType'] == XMLReader::ELEMENT &&
            $this->_properties['isEmptyElement'] == TRUE) {
            $this->_properties['nodeType'] = XMLReader::END_ELEMENT;
            $this->_properties['isEmptyElement'] = FALSE;
            return TRUE;
        }
        return FALSE;
    }
}

