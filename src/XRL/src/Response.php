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

/**
 * \brief
 *      This class represents the response
 *      to an XML-RPC request.
 */
class       XRL_Response
implements  XRL_ResponseInterface
{
    /// The result of an XML-RPC request, as serialized XML.
    protected $_result;

    /**
     * Create the response to an XML-RPC request.
     *
     * \param mixed $xmlResult
     *      The result of the request. This may be a scalar
     *      (integer, boolean, float, string), an array,
     *      an exception or a DateTime object.
     */
    public function __construct($xmlResult)
    {
        $this->_result  = $xmlResult;
    }

    /**
     * Returns the response for an XML-RPC request,
     * as serialized XML.
     *
     * \retval string
     *      An XML-RPC response, as a string.
     */
    public function __string()
    {
        return $this->_result;
    }

    /**
     * Send this XML-RPC response back to a browser.
     *
     * \warning
     *      This method never returns.
     */
    public function publish()
    {
        header('Content-Type: text/xml');
        header('Content-Length: '.strlen($this->_result));
        exit($this->_result);
    }
}
