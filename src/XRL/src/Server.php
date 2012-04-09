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
 *      A simple XML-RPC server.
 *
 * Instances of this class can be used as an array:
 * \code
 *      // This registers the procedure "foo"
 *      // on this XML-RPC server. The function
 *      // "bar" will be called to handle calls
 *      // to "foo".
 *      $server->foo = 'bar';
 *
 *      // This returns the callable used to handle
 *      // calls to "foo", wrapped in an object
 *      // implementing the "XRL_CallableInterface"
 *      // interface.
 *      $foo = $server->foo;
 *
 *      // This tests whether the "foo" procedure
 *      // has been registered on this server.
 *      if (isset($server->foo)) {
 *          ...
 *      }
 *
 *      // This unregisters the "foo" procedure
 *      // from this XML-RPC server.
 *      unset($server->foo);
 * \endcode
 *
 * You may also count how many XML-RPC procedures
 * are currently registered on this server:
 * \code
 *      $nbProcedures = count($server);
 * \endcode
 *
 * Last but not least, you may also iterate over
 * this server's registered XML-RPC procedures:
 * \code
 *      foreach ($server as $procedure) {
 *          ...
 *      }
 * \endcode
 */
class       XRL_Server
extends     XRL_FactoryRegistry
implements  Countable,
            IteratorAggregate
{
    /// Registered "procedures".
    protected $_funcs;

    /**
     * Create a new XML-RPC server.
     */
    public function __construct()
    {
        $this->_funcs           = array();
        $this->_interfaces      = array(
            'xrl_encoderfactoryinterface'   =>
                new XRL_CompactEncoderFactory(),

            'xrl_decoderfactoryinterface'   =>
                new XRL_ValidatingDecoderFactory(),

            'xrl_callablefactoryinterface'  =>
                new XRL_CallableFactory(),

            'xrl_responsefactoryinterface'  =>
                new XRL_ResponseFactory(),
        );
    }

    /**
     * Register a new procedure with this XML-RPC server.
     *
     * \param string $func
     *      A valid name for the procedure.
     *
     * \param mixed $callback
     *      Any valid PHP callback.
     *
     * \note
     *      See the "Payload format" section at
     *      http://xmlrpc.scripting.com/spec.html
     *      for information on valid procedure names.
     *
     * \note
     *      Several syntaxes can be used to refer to a PHP callback, see
     *      http://php.net/language.pseudo-types.php#language.types.callback
     *      for the full list of supported constructs.
     */
    public function __set($func, $callback)
    {
        $factory    = $this['XRL_CallableFactoryInterface'];
        $callable   = $factory->fromPHP($callback);
        assert($callable instanceof XRL_CallableInterface);
        $this->_funcs[$func] = $callable;
    }

    /**
     * Return a procedure previously registered
     * with this XML-RPC server.
     *
     * \param string $func
     *      The name of the registered XML-RPC procedure
     *      to return.
     *
     * \retval XRL_CallableInterface
     *      The callable responsible for the XML-RPC
     *      procedure registered with the given name.
     *
     * \retval NULL
     *      The given $func does not refer to an XML-RPC
     *      procedure registered with this server.
     *
     * \note
     *      In case the given procedure has not been
     *      registered, a PHP notice will be issued.
     */
    public function __get($func)
    {
        return $this->_funcs[$func];
    }

    /**
     * Test whether a procedure has been registered
     * with the given name on this server.
     *
     * \param string $func
     *      Name of the procedure whose existence
     *      must be verified.
     *
     * \retval bool
     *      TRUE if the procedure exists,
     *      FALSE otherwise.
     */
    public function __isset($func)
    {
        return isset($this->_funcs[$func]);
    }

    /**
     * Unregister a procedure.
     *
     * \param string $func
     *      The name of the procedure to unregister.
     *
     * \note
     *      No warning will be emitted if the given
     *      procedure has not been registered
     *      on this XML-RPC server.
     */
    public function __unset($func)
    {
        unset($this->_funcs[$func]);
    }

    /**
     * Return the number of XML-RPC procedures
     * currently registered on this server.
     *
     * \retval int
     *      Number of currently registered
     *      procedures on this server.
     */
    public function count()
    {
        return count($this->_funcs);
    }

    /**
     * Get an iterator over this server's
     * registered XML-RPC procedures.
     *
     * \retval ArrayIterator
     *      An iterator over this server's
     *      registered procedures.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_funcs);
    }

    /**
     * Handles an XML-RPC request and returns a response
     * for that request.
     *
     * \retval XRL_ResponseInterface
     *      The response for that request. This response
     *      may indicate either success or failure of the
     *      Remote Procedure Call 
     */
    public function handle($data = NULL)
    {
        if ($data === NULL)
            $data = file_get_contents('php://input');

        $factory    = $this['XRL_EncoderFactoryInterface'];
        $encoder    = $factory->createEncoder();
        assert($encoder instanceof XRL_EncoderInterface);

        $factory    = $this['XRL_DecoderFactoryInterface'];
        $decoder    = $factory->createDecoder();
        assert($decoder instanceof XRL_DecoderInterface);

        try {
            $request    = $decoder->decodeRequest($data);
            $procedure  = $request->getProcedure();

            if (!isset($this->_funcs[$procedure])) {
                throw new BadFunctionCallException(
                    "No such procedure ($procedure)"
                );
            }

            $callable   = $this->_funcs[$procedure];
            $params     = $request->getParams();
            $result     = $callable->invokeArgs($params);
            $response   = $encoder->encodeResponse($result);
        }
        catch (Exception $result) {
            $response   = $encoder->encodeError($result);
        }

        $factory = $this['XRL_ResponseFactoryInterface'];
        $returnValue = $factory->createResponse($response);
        assert($returnValue instanceof XRL_ResponseInterface);
        return $returnValue;
    }
}

