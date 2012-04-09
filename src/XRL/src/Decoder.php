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

class       XRL_Decoder
implements  XRL_DecoderInterface
{
    protected $_validate;
    protected $_currentNode;

    public function __construct($validate = TRUE)
    {
        if (!is_bool($validate))
            ; /// @TODO

        $this->_validate    = $validate;
        $this->_currentNode = NULL;
    }

    protected function _getReader($data, $request)
    {
        if (!is_bool($request))
            ; /// @TODO

        $this->_currentNode = NULL;
        $reader = new XMLReader();
        $reader->xml($data, NULL, LIBXML_NONET | LIBXML_NOENT);
        if ($this->_validate) {
            if ('@data_dir@' != '@'.'data_dir'.'@') {
                $schema = '@data_dir@' .
                    DIRECTORY_SEPARATOR . 'pear.erebot.net' .
                    DIRECTORY_SEPARATOR . 'XRL';
            }
            else
                $schema = dirname(dirname(dirname(__FILE__))) .
                    DIRECTORY_SEPARATOR . 'data';

            $schema .= DIRECTORY_SEPARATOR;
            $schema .= $request ? 'request.rng' : 'response.rng';
            $reader->setRelaxNGSchema($schema);
        }
        return $reader;
    }

    protected function _readNode($reader)
    {
        if ($this->_currentNode !== NULL)
            return $this->_currentNode;

        $this->_currentNode = new XRL_Node($reader, $this->_validate);
        return $this->_currentNode;
    }

    protected function _prepareNextNode()
    {
        if (!$this->_currentNode->emptyNodeExpansionWorked())
            $this->_currentNode = NULL;
    }

    protected function _expectStartTag($reader, $expectedTag)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::ELEMENT) {
            throw new InvalidArgumentException(
                "Expected an opening $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new InvalidArgumentException(
                "Got opening tag for $readTag instead of $expectedTag"
            );
        }

        $this->_prepareNextNode();
    }

    protected function _expectEndTag($reader, $expectedTag)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::END_ELEMENT) {
            throw new InvalidArgumentException(
                "Expected a closing $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new InvalidArgumentException(
                "Got closing tag for $readTag instead of $expectedTag"
            );
        }

        $this->_prepareNextNode();
    }

    protected function _parseText($reader)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::TEXT) {
            throw new InvalidArgumentException(
                "Expected a text node, but got ".
                "a node of type #$type instead"
            );
        }

        $value              = $node->value;
        $this->_prepareNextNode();
        return $value;
    }

    static protected function _checkType(array $allowedTypes, $type, $value)
    {
        if (count($allowedTypes) && !in_array($type, $allowedTypes)) {
            $allowed = implode(', ', $allowedTypes);
            throw new InvalidArgumentException(
                "Expected one of: $allowed, but got $type"
            );
        }

        return $value;
    }

    protected function _decodeValue($reader, array $allowedTypes = array())
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'nil');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $this->_expectEndTag($reader, 'nil');
            return self::_checkType($allowedTypes, 'nil', NULL);
        }

        // Other basic types.
        $types = array(
            'i4',
            'int',
            'boolean',
            'string',
            'double',
            'dateTime.iso8601',
            'base64',
        );

        foreach ($types as $type) {
            try {
                $this->_expectStartTag($reader, $type);
            }
            catch (InvalidArgumentException $e) {
                continue;
            }

            $value = $this->_parseText($reader);
            $this->_expectEndTag($reader, $type);

            switch ($type) {
                case 'i4':
                    // "i4" is an alias for "int".
                    $type = 'int';
                case 'int':
                    $value = (int) $value;
                    break;

                case 'boolean':
                    $value = (bool) $value;
                    break;

                case 'string':
                    break;

                case 'double':
                    $value = (double) $value;
                    break;

                case 'dateTime.iso8601':
                    $value = NULL; /// @TODO
                    break;

                case 'base64':
                    $value = base64_decode($value);
                    break;
            }

            return self::_checkType($allowedTypes, $type, $value);
        }

        // Handle structures.
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'struct');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            // Read values.
            while (TRUE) {
                $error = NULL;
                try {
                    $this->_expectStartTag($reader, 'member');
                }
                catch (InvalidArgumentException $error) {
                }

                if ($error)
                    break;

                // Read key.
                $this->_expectStartTag($reader, 'name');
                $key = $this->_decodeValue($reader, array('string', 'int'));
                $this->_expectEndTag($reader, 'name');

                $this->_expectStartTag($reader, 'value');
                $value[$key] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
                $this->_expectEndTag($reader, 'member');
            }
            $this->_expectEndTag($reader, 'struct');
            return self::_checkType($allowedTypes, 'struct', $value);
        }

        // Handle arrays.
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'array');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            $this->_expectStartTag($reader, 'data');
            // Read values.
            while (TRUE) {
                $error = NULL;
                try {
                    $this->_expectStartTag($reader, 'value');
                }
                catch (InvalidArgumentException $error) {
                }

                if ($error)
                    break;

                $value[] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
            }
            $this->_expectEndTag($reader, 'data');
            $this->_expectEndTag($reader, 'array');
            return self::_checkType($allowedTypes, 'array', $value);
        }

        // Default type (string).
        try {
            $value = $this->_parseText($reader);
        }
        catch (InvalidArgumentException $e) {
            $value = '';
        }
        return self::_checkType($allowedTypes, 'string', $value);
    }

    public function decodeRequest($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $reader = $this->_getReader($data, TRUE);
        $this->_expectStartTag($reader, 'methodCall');
        $this->_expectStartTag($reader, 'methodName');
        $methodName = $this->_parseText($reader);
        $this->_expectEndTag($reader, 'methodName');

        $params         = array();
        $emptyParams    = NULL;
        try {
            $this->_expectStartTag($reader, 'params');
        }
        catch (InvalidArgumentException $emptyParams) {
            // Nothing to do here (no arguments given).
        }

        if (!$emptyParams) {
            $endOfParams = NULL;
            while (TRUE) {
                try {
                    $this->_expectStartTag($reader, 'param');
                }
                catch (InvalidArgumentException $endOfParams) {
                    // Nothing to do here (end of arguments).
                }

                if ($endOfParams)
                    break;

                $this->_expectStartTag($reader, 'value');
                $params[] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
                $this->_expectEndTag($reader, 'param');
            }
            $this->_expectEndTag($reader, 'params');
        }
        $this->_expectEndTag($reader, 'methodCall');

        $endOfFile = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile)
            throw new InvalidArgumentException('Expected end of document');

        $request = new XRL_Request($methodName, $params);
        return $request;
    }

    public function decodeResponse($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $error  = NULL;
        $reader = $this->_getReader($data, FALSE);
        $this->_expectStartTag($reader, 'methodResponse');
        try {
            // Try to parse a successful response first.
            $this->_expectStartTag($reader, 'params');
            $this->_expectStartTag($reader, 'param');
            $this->_expectStartTag($reader, 'value');
            $response = $this->_decodeValue($reader);
            $this->_expectEndTag($reader, 'value');
            $this->_expectEndTag($reader, 'param');
            $this->_expectEndTag($reader, 'params');
        }
        catch (InvalidArgumentException $error) {
            // Try to parse a fault instead.
            $this->_expectStartTag($reader, 'fault');
            $this->_expectStartTag($reader, 'value');

            $response = $this->_decodeValue($reader);
            if (!is_array($response) || count($response) != 2) {
                throw new UnexpectedValueException(
                    'An associative array with exactly '.
                    'two entries was expected'
                );
            }

            if (!isset($response['faultCode']))
                throw new DomainException('The failure lacks a faultCode');

            if (!isset($response['faultString']))
                throw new DomainException('The failure lacks a faultString');

            $this->_expectEndTag($reader, 'value');
            $this->_expectEndTag($reader, 'fault');
        }
        $this->_expectEndTag($reader, 'methodResponse');

        $endOfFile = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile)
            throw new InvalidArgumentException('Expected end of document');

        if ($error) {
            throw new XRL_Exception(
                $response['faultString'],
                $response['faultCode']
            );
        }

        return $response;
    }
}

