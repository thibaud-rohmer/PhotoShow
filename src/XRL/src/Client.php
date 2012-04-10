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
 *      A simple XML-RPC client.
 *
 * To call a remote XML procedure, create a new instance
 * of this class (pass the server's URL to the constructor)
 * and then simply call the procedure as if it was a method
 * of the object returned:
 *
 * \code
 *      $client = new XRL_Client("http://xmlrpc.example.com/");
 *      // This calls the remote procedure "foo"
 *      // and prints the result of that call.
 *      var_dump($client->foo(42));
 * \endcode
 *
 * In case the remote procedure's name is not a valid
 * PHP identifier, you may still call it using the
 * curly braces notation:
 *
 * \code
 *      // Calls the remote procedure named "foo.bar.baz".
 *      $client->{"foo.bar.baz"}(42);
 * \endcode
 */
class   XRL_Client
extends XRL_FactoryRegistry
{
    /// The remote XML-RPC server's base URL.
    protected $_baseURL;

    /// A DateTimeZone object representing the server's timezone.
    protected $_timezone;

    /// A stream context to use when querying the server.
    protected $_context;

    /// Callable used to fetch the response.
    protected $_fetcher;

    /**
     * Create a new XML-RPC client.
     *
     * \param string $baseURL
     *      Base URL for the XML-RPC server,
     *      eg. "http://www.example.com/xmlrpc/".
     *
     * \param string $timezone
     *      (optional) The name of the timezone the remote server
     *      is in (eg. "Europe/Paris"). This parameter is used
     *      to represent dates and times using the proper timezone
     *      before sending them to the server.
     *      If omitted, the client's current timezone is used.
     *
     * \param resource $context
     *      (optional) A PHP stream context to use
     *      when querying the remote XML-RPC server.
     *
     * \note
     *      See http://php.net/manual/en/timezones.php for a list
     *      of valid timezone names supported by PHP.
     *
     * \note
     *      See http://php.net/manual/en/stream.contexts.php
     *      for more information about PHP stream contexts.
     *
     * \throw InvalidArgumentException
     *      The given timezone or context is invalid.
     */
    public function __construct(
                                $baseURL,
                                $timezone   = NULL,
                                $context    = NULL
    )
    {
        if ($timezone === NULL)
            $timezone = date_default_timezone_get();
        if ($context === NULL)
            $context = stream_context_get_default();

        if (!is_resource($context))
            throw new InvalidArgumentException('Invalid context');

        $this->_baseURL = $baseURL;
        try {
            $this->_timezone = new DateTimeZone($timezone);
        }
        catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode());
        }

        $this->_context     = $context;
        $this->_fetcher     = 'file_get_contents';
        $this->_interfaces  = array(
            'xrl_encoderfactoryinterface'   =>
                new XRL_CompactEncoderFactory(),

            'xrl_decoderfactoryinterface'   =>
                new XRL_ValidatingDecoderFactory(),

            'xrl_requestfactoryinterface'   =>
                new XRL_RequestFactory(),
        );
    }

    /**
     * A magic method that forwards all method calls
     * to the remote XML-RPC server and returns
     * that server's response on success or throws
     * an exception on failure.
     *
     * \param string $method
     *      The remote procedure to call.
     *
     * \param array $args
     *      A list of arguments to pass to the remote
     *      procedure.
     *
     * \retval mixed
     *      The remote server's response, as a native
     *      type (string, int, boolean, float or
     *      DateTime object).
     *
     * \throw XRL_Exception
     *      Raised in case the remote server's response
     *      indicates some kind of error. You may use
     *      this exception's getCode() and getMessage()
     *      methods to find out more about the error.
     *
     * \throw @TODO: decide on what exception must be raised here.
     *      Raised when this client wasn't able to query
     *      the remote server (such as when no connection
     *      could be established to it).
     */
    public function __call($method, array $args)
    {
        $factory    = $this['XRL_RequestFactoryInterface'];
        $request    = $factory->createRequest($method, $args);
        assert($request instanceof XRL_RequestInterface);

        $factory    = $this['XRL_EncoderFactoryInterface'];
        $encoder    = $factory->createEncoder();
        assert($encoder instanceof XRL_EncoderInterface);

        $factory    = $this['XRL_DecoderFactoryInterface'];
        $decoder    = $factory->createDecoder();
        assert($decoder instanceof XRL_DecoderInterface);

        $xml        = $encoder->encodeRequest($request);
        $options    = array(
            'http' => array(
                'method'    => 'POST',
                'content'   => $xml,
                'header'    => 'Content-Type: text/xml',
            ),
        );
        stream_context_set_option($this->_context, $options);

        $data = call_user_func(
            $this->_fetcher,
            $this->_baseURL,
            FALSE,
            $this->_context
        );
        $result = $decoder->decodeResponse($data);
        return $result;
    }
}

