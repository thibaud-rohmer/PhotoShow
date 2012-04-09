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

class       XRL_Encoder
implements  XRL_EncoderInterface
{
    /// Make the output compact.
    const OUTPUT_COMPACT    = 0;

    /// Make the output pretty.
    const OUTPUT_PRETTY     = 1;

    protected $_format;

    public function __construct($format = self::OUTPUT_COMPACT)
    {
        if ($format != self::OUTPUT_PRETTY &&
            $format != self::OUTPUT_COMPACT)
            throw new InvalidArgumentException('Invalid format');

        $this->_format = $format;
    }

    protected function _getWriter()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        if ($this->_format == self::OUTPUT_PRETTY) {
             $writer->setIndent(TRUE);
            $writer->startDocument('1.0', 'UTF-8');
        }
        else {
            $writer->setIndent(FALSE);
            $writer->startDocument();
        }
        return $writer;
    }

    /**
     * Can be used to determine if a string contains a sequence
     * of valid UTF-8 encoded codepoints.
     *
     * \param string $text
     *      Some text to test for UTF-8 correctness.
     *
     * \retval bool
     *      TRUE if the $text contains a valid UTF-8 sequence,
     *      FALSE otherwise.
     */
    static protected function _isUTF8($text)
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        // Pointed out by bitseeker on http://php.net/utf8_encode
        return (bool) preg_match(
            '%^(?:
                  [\x09\x0A\x0D\x20-\x7E]            # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%SDxs', $text
        );
    }

    static protected function _writeValue(XMLWriter $writer, $value)
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        if (is_null($value))
            return $writer->writeElement('nil');

        if (is_int($value))
            return $writer->writeElement('int', $value);

        if (is_bool($value))
            return $writer->writeElement('boolean', (int) $value);

        if (is_string($value)) {
            // Encode as a regular string if possible.
            if (self::_isUTF8($value))
                return $writer->text($value);
            // Otherwise, use a base64-encoded string.
            return $writer->writeElement('base64', base64_encode($value));
        }

        if (is_double($value))
            return $writer->writeElement('double', $value);

        if (is_array($value)) {
            $keys       = array_keys($value);
            $length     = count($value);

            // Empty arrays must be handled with care.
            if (!$length)
                $numeric = array();
            else {
                $numeric = range(0, $length - 1);
                sort($keys);
            }

            // Hash / associative array.
            if ($keys != $numeric) {
                $writer->startElement('struct');
                foreach ($value as $key => $val) {
                    $writer->startElement('member');
                    $writer->startElement('name');
                    self::_writeValue($writer, $key);
                    $writer->endElement();

                    $writer->startElement('value');
                    self::_writeValue($writer, $val);
                    $writer->endElement();
                    $writer->endElement();
                }
                $writer->endElement();
                return;
            }

            // List / numerically-indexed array.
            $writer->startElement('array');
            $writer->startElement('data');
            foreach ($value as $val) {
                $writer->startElement('value');
                self::_writeValue($writer, $val);
                $writer->endElement();
            }
            $writer->endElement();
            $writer->endElement();
            return;
        }

        if (!is_object($value))
            throw new InvalidArgumentException('Unsupported type');

        /// @TODO: special support for DateTime objects.

        if (($value instanceof Serializable) ||
            method_exists($value, '__sleep'))
            return $this->_writeValue($writer, serialize($value));

        throw new InvalidArgumentException('Could not serialize object');
    }

    protected function _finalizeWrite(XMLWriter $writer)
    {
        $writer->endDocument();
        $result = $writer->outputMemory(TRUE);

        if ($this->_format == self::OUTPUT_COMPACT) {
            // Remove the XML declaration for an even
            // more compact result.
            if (!strncmp($result, '<'.'?xml', 5)) {
                $pos    = strpos($result, '?'.'>');
                if ($pos !== FALSE)
                    $result = (string) substr($result, $pos + 2);
            }
            // Remove leading & trailing whitespace.
            $result = trim($result);
        }

        return $result;
    }

    public function encodeRequest(XRL_Request $request)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodCall');
        $writer->writeElement('methodName', $request->getProcedure());
        if (count($request->getParams())) {
            $writer->startElement('params');
            foreach ($request->getParams() as $param) {
                $writer->startElement('param');
                $writer->startElement('value');
                self::_writeValue($writer, $param);
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }

    public function encodeError(Exception $error)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('fault');
        $writer->startElement('value');
        self::_writeValue(
            $writer,
            array(
                'faultCode'     => $error->getCode(),
                'faultString'   => get_class($error).': '.$error->getMessage(),
            )
        );
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }

    public function encodeResponse($response)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('params');
        $writer->startElement('param');
        $writer->startElement('value');
        self::_writeValue($writer, $response);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }
}

