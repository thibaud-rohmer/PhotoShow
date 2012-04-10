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
 *      A class that represents an XML-RPC request.
 */
class       XRL_Request
implements  XRL_RequestInterface
{
    /// Name of the remote procedure to call.
    protected $_procedure;

    /// Parameters to pass to the remote procedure.
    protected $_params;

    /**
     * Creates a new XML-RPC request.
     *
     * \param string $procedure
     *      Name of the remote procedure to call.
     *
     * \param array $params
     *      Parameters to pass to the remote procedure.
     *
     * \throw InvalidArgumentException
     *      An invalid procedure name was given.
     */
    public function __construct($procedure, array $params)
    {
        if (!is_string($procedure))
            throw new InvalidArgumentException('Invalid procedure name');

        $this->_procedure   = $procedure;
        $this->_params      = $params;
    }

    /**
     * Returns the remote procedure's name.
     *
     * \retval string
     *      The name of the remote procedure this request
     *      is meant to call.
     */
    public function getProcedure()
    {
        return $this->_procedure;
    }

    /**
     * Returns the parameters to pass
     * to the remote procedure.
     *
     * \retval array
     *      Parameters to pass to the
     *      remote procedure.
     */
    public function getParams()
    {
        return $this->_params;
    }
}
