<?php
/**
 * Socket-based adapter for HTTP_Request2
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2008, 2009, Alexey Borzov <avb@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTTP
 * @package    HTTP_Request2
 * @author     Alexey Borzov <avb@php.net>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id: Socket.php,v 1.6 2009/01/07 19:28:22 avb Exp $
 * @link       http://pear.php.net/package/HTTP_Request2
 */

/**
 * Base class for HTTP_Request2 adapters
 */
require_once 'HTTP/Request2/Adapter.php';

/**
 * Socket-based adapter for HTTP_Request2
 *
 * This adapter uses only PHP sockets and will work on almost any PHP
 * environment. Code is based on original HTTP_Request PEAR package.
 *
 * @category    HTTP
 * @package     HTTP_Request2
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 0.2.0
 * @todo        Implement HTTPS proxy support via stream_socket_enable_crypto()
 * @todo        Implement Digest authentication support
 */
class HTTP_Request2_Adapter_Socket extends HTTP_Request2_Adapter
{
   /**
    * Connected sockets, needed for Keep-Alive support
    * @var  array
    * @see  connect()
    */
    protected static $sockets;

   /**
    * Connected socket
    * @var  resource
    * @see  connect()
    */
    protected $socket;

   /**
    * Global timeout, exception will be raised if request continues past this time
    * @var  integer
    */
    protected $timeout = null;

   /**
    * Remaining length of the current chunk, when reading chunked response
    * @var  integer
    * @see  readChunked()
    */ 
    protected $chunkLength = 0;

   /**
    * Sends request to the remote server and returns its response
    *
    * @param    HTTP_Request2
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    public function sendRequest(HTTP_Request2 $request)
    {
        $this->request = $request;
        $keepAlive     = $this->connect();
        $headers       = $this->prepareHeaders();

        // Use global request timeout if given, see feature requests #5735, #8964 
        if ($timeout = $request->getConfig('timeout')) {
            $this->timeout = time() + $timeout;
        } else {
            $this->timeout = null;
        }

        try {
            if (false === @fwrite($this->socket, $headers, strlen($headers))) {
                throw new HTTP_Request2_Exception('Error writing request');
            }
            // provide request headers to the observer, see request #7633
            $this->request->setLastEvent('sentHeaders', $headers);
            $this->writeBody();

            if ($this->timeout && time() > $this->timeout) {
                throw new HTTP_Request2_Exception(
                    'Request timed out after ' . 
                    $request->getConfig('timeout') . ' second(s)'
                );
            }

            $response = $this->readResponse();

            // check whether we should keep the connection open
            $lengthKnown = 'chunked' == strtolower($response->getHeader('transfer-encoding')) ||
                           null !== $response->getHeader('content-length');
            $persistent  = 'keep-alive' == strtolower($response->getHeader('connection')) ||
                           (null === $response->getHeader('connection') &&
                            '1.1' == $response->getVersion());
            if (!$keepAlive || !$lengthKnown || !$persistent) {
                $this->disconnect();
            }

        } catch (Exception $e) {
            $this->disconnect();
            throw $e;
        }
        return $response;
    }

   /**
    * Connects to the remote server
    *
    * @return   bool    whether the connection can be persistent
    * @throws   HTTP_Request2_Exception
    */
    protected function connect()
    {
        if ($host = $this->request->getConfig('proxy_host')) {
            if (!($port = $this->request->getConfig('proxy_port'))) {
                throw new HTTP_Request2_Exception('Proxy port not provided');
            }
            $proxy = true;
        } else {
            $host = $this->request->getUrl()->getHost();
            if (!($port = $this->request->getUrl()->getPort())) {
                $port = 0 == strcasecmp(
                            $this->request->getUrl()->getScheme(), 'https'
                        )? 443: 80;
            }
            $proxy = false;
        }

        $options = array();
        if (0 != strcasecmp($this->request->getUrl()->getScheme(), 'https')) {
            $host = 'tcp://' . $host;
        } else {
            if ($proxy) {
                throw new HTTP_Request2_Exception('HTTPS proxy support not yet implemented');
            } elseif (!in_array('ssl', stream_get_transports())) {
                throw new HTTP_Request2_Exception('Need OpenSSL support for https:// requests');
            }
            $host = 'ssl://' . $host;
            foreach ($this->request->getConfig() as $name => $value) {
                if ('ssl_' == substr($name, 0, 4) && null !== $value) {
                    if ('ssl_verify_host' == $name) {
                        if ($value) {
                            $options['CN_match'] = $this->request->getUrl()->getHost();
                        }
                    } else {
                        $options[substr($name, 4)] = $value;
                    }
                }
            }
            ksort($options);
        }

        $headers = $this->request->getHeaders();

        // RFC 2068, section 19.7.1: A client MUST NOT send the Keep-Alive
        // connection token to a proxy server...
        if ($proxy && !empty($headers['connection']) && 'Keep-Alive' == $headers['connection']) {
            $this->request->setHeader('connection');
        }

        $keepAlive = ('1.1' == $this->request->getConfig('protocol_version') && 
                      empty($headers['connection'])) ||
                     (!empty($headers['connection']) &&
                      'Keep-Alive' == $headers['connection']);
        // Changing SSL context options after connection is established does *not*
        // work, we need a new connection if options change
        $remote    = $host . ':' . $port;
        $socketKey = $remote . (empty($options)? '': ':' . serialize($options));
        unset($this->socket);

        // We use persistent connections and have a connected socket?
        if ($keepAlive && !empty(self::$sockets[$socketKey])) {
            $this->socket =& self::$sockets[$socketKey];
        } else {
            // Set SSL context options if doing HTTPS request
            $context = stream_context_create();
            foreach ($options as $name => $value) {
                if (!stream_context_set_option($context, 'ssl', $name, $value)) {
                    throw new HTTP_Request2_Exception(
                        "Error setting SSL context option '{$name}'"
                    );
                }
            }
            $this->socket = @stream_socket_client(
                $remote, $errno, $errstr,
                $this->request->getConfig('connect_timeout'),
                STREAM_CLIENT_CONNECT, $context
            );
            if (!$this->socket) {
                throw new HTTP_Request2_Exception(
                    "Unable to connect to {$remote}. Error #{$errno}: {$errstr}"
                );
            }
            $this->request->setLastEvent('connect', $remote);
            self::$sockets[$socketKey] =& $this->socket;
        }
        return $keepAlive;
    }

   /**
    * Disconnects from the remote server
    */
    protected function disconnect()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->request->setLastEvent('disconnect');
        }
    }

   /**
    * Creates the value for '[Proxy-]Authorization:' header
    *
    * @param    string  user name
    * @param    string  password
    * @param    string  authentication scheme, one of the HTTP_Request2::AUTH_* constants
    * @return   string  header value
    * @throws   HTTP_Request2_Exception
    */
    protected function createAuthHeader($user, $pass, $scheme)
    {
        switch ($scheme) {
            case HTTP_Request2::AUTH_BASIC:
                return 'Basic ' . base64_encode($user . ':' . $pass);

            case HTTP_Request2::AUTH_DIGEST:
                throw new HTTP_Request2_Exception('Digest authentication is not implemented yet.');

            default:
                throw new HTTP_Request2_Exception("Unknown HTTP authentication scheme '{$scheme}'");
        }
    }

   /**
    * Creates the string with the Request-Line and request headers
    *
    * @return   string
    * @throws   HTTP_Request2_Exception
    */
    protected function prepareHeaders()
    {
        $headers = $this->request->getHeaders();
        $url     = $this->request->getUrl();

        $host    = $url->getHost();
        if ($port = $url->getPort()) {
            $scheme = $url->getScheme();
            if ((0 == strcasecmp($scheme, 'http') && 80 != $port) ||
                (0 == strcasecmp($scheme, 'https') && 443 != $port)
            ) {
                $host .= ':' . $port;
            }
        }
        $headers['host'] = $host;

        if (!$this->request->getConfig('proxy_host')) {
            $requestUrl = '';
        } else {
            if ($user = $this->request->getConfig('proxy_user')) {
                $headers['proxy-authorization'] = $this->createAuthHeader(
                    $user, $this->request->getConfig('proxy_password'),
                    $this->request->getConfig('proxy_auth_scheme')
                );
            }
            $requestUrl = $url->getScheme() . '://' . $host;
        }
        $path        = $url->getPath();
        $query       = $url->getQuery();
        $requestUrl .= (empty($path)? '/': $path) . (empty($query)? '': '?' . $query);

        if ($auth = $this->request->getAuth()) {
            $headers['authorization'] = $this->createAuthHeader(
                $auth['user'], $auth['password'], $auth['scheme']
            );
        }
        if ('1.1' == $this->request->getConfig('protocol_version') &&
            extension_loaded('zlib') && !isset($headers['accept-encoding'])
        ) {
            $headers['accept-encoding'] = 'gzip, deflate';
        }

        $this->calculateRequestLength($headers);

        $headersStr = $this->request->getMethod() . ' ' . $requestUrl . ' HTTP/' .
                      $this->request->getConfig('protocol_version') . "\r\n";
        foreach ($headers as $name => $value) {
            $canonicalName = implode('-', array_map('ucfirst', explode('-', $name)));
            $headersStr   .= $canonicalName . ': ' . $value . "\r\n";
        }
        return $headersStr . "\r\n";
    }

   /**
    * Sends the request body
    *
    * @throws   HTTP_Request2_Exception
    */
    protected function writeBody()
    {
        if (in_array($this->request->getMethod(), self::$bodyDisallowed) ||
            0 == $this->contentLength
        ) {
            return;
        }

        $position   = 0;
        $bufferSize = $this->request->getConfig('buffer_size');
        while ($position < $this->contentLength) {
            if (is_string($this->requestBody)) {
                $str = substr($this->requestBody, $position, $bufferSize);
            } elseif (is_resource($this->requestBody)) {
                $str = fread($this->requestBody, $bufferSize);
            } else {
                $str = $this->requestBody->read($bufferSize);
            }
            if (false === @fwrite($this->socket, $str, strlen($str))) {
                throw new HTTP_Request2_Exception('Error writing request');
            }
            // Provide the length of written string to the observer, request #7630
            $this->request->setLastEvent('sentBodyPart', strlen($str));
            $position += strlen($str); 
        }
    }

   /**
    * Reads the remote server's response
    *
    * @return   HTTP_Request2_Response
    * @throws   HTTP_Request2_Exception
    */
    protected function readResponse()
    {
        $bufferSize = $this->request->getConfig('buffer_size');

        do {
            $response = new HTTP_Request2_Response($this->readLine($bufferSize), true);
            do {
                $headerLine = $this->readLine($bufferSize);
                $response->parseHeaderLine($headerLine);
            } while ('' != $headerLine);
        } while (in_array($response->getStatus(), array(100, 101)));

        $this->request->setLastEvent('receivedHeaders', $response);

        // No body possible in such responses
        if (HTTP_Request2::METHOD_HEAD == $this->request->getMethod() ||
            in_array($response->getStatus(), array(204, 304))
        ) {
            return $response;
        }

        $chunked = 'chunked' == $response->getHeader('transfer-encoding');
        $length  = $response->getHeader('content-length');
        $hasBody = false;
        if ($chunked || null === $length || 0 < intval($length)) {
            // RFC 2616, section 4.4:
            // 3. ... If a message is received with both a
            // Transfer-Encoding header field and a Content-Length header field,
            // the latter MUST be ignored.
            $toRead = ($chunked || null === $length)? null: $length;
            $this->chunkLength = 0;

            while (!feof($this->socket) && (is_null($toRead) || 0 < $toRead)) {
                if ($chunked) {
                    $data = $this->readChunked($bufferSize);
                } elseif (is_null($toRead)) {
                    $data = $this->fread($bufferSize);
                } else {
                    $data    = $this->fread(min($toRead, $bufferSize));
                    $toRead -= strlen($data);
                }
                if ('' == $data && (!$this->chunkLength || feof($this->socket))) {
                    break;
                }

                $hasBody = true;
                $response->appendBody($data);
                if (!in_array($response->getHeader('content-encoding'), array('identity', null))) {
                    $this->request->setLastEvent('receivedEncodedBodyPart', $data);
                } else {
                    $this->request->setLastEvent('receivedBodyPart', $data);
                }
            }
        }

        if ($hasBody) {
            $this->request->setLastEvent('receivedBody', $response);
        }
        return $response;
    }

   /**
    * Reads until either the end of the socket or a newline, whichever comes first 
    *
    * Strips the trailing newline from the returned data, handles global 
    * request timeout. Method idea borrowed from Net_Socket PEAR package. 
    *
    * @param    int     buffer size to use for reading
    * @return   Available data up to the newline (not including newline)
    * @throws   HTTP_Request2_Exception     In case of timeout
    */
    protected function readLine($bufferSize)
    {
        $line = '';
        while (!feof($this->socket)) {
            if ($this->timeout) {
                stream_set_timeout($this->socket, max($this->timeout - time(), 1));
            }
            $line .= @fgets($this->socket, $bufferSize);
            $info  = stream_get_meta_data($this->socket);
            if ($info['timed_out'] || $this->timeout && time() > $this->timeout) {
                throw new HTTP_Request2_Exception(
                    'Request timed out after ' . 
                    $this->request->getConfig('timeout') . ' second(s)'
                );
            }
            if (substr($line, -1) == "\n") {
                return rtrim($line, "\r\n");
            }
        }
        return $line;
    }

   /**
    * Wrapper around fread(), handles global request timeout
    *
    * @param    int     Reads up to this number of bytes
    * @return   Data read from socket
    * @throws   HTTP_Request2_Exception     In case of timeout
    */
    protected function fread($length)
    {
        if ($this->timeout) {
            stream_set_timeout($this->socket, max($this->timeout - time(), 1));
        }
        $data = fread($this->socket, $length);
        $info = stream_get_meta_data($this->socket);
        if ($info['timed_out'] || $this->timeout && time() > $this->timeout) {
            throw new HTTP_Request2_Exception(
                'Request timed out after ' . 
                $this->request->getConfig('timeout') . ' second(s)'
            );
        }
        return $data;
    }

   /**
    * Reads a part of response body encoded with chunked Transfer-Encoding
    *
    * @param    int     buffer size to use for reading
    * @return   string
    * @throws   HTTP_Request2_Exception
    */
    protected function readChunked($bufferSize)
    {
        // at start of the next chunk?
        if (0 == $this->chunkLength) {
            $line = $this->readLine($bufferSize);
            if (!preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
                throw new HTTP_Request2_Exception(
                    "Cannot decode chunked response, invalid chunk length '{$line}'"
                );
            } else {
                $this->chunkLength = hexdec($matches[1]);
                // Chunk with zero length indicates the end
                if (0 == $this->chunkLength) {
                    $this->readLine($bufferSize);
                    return '';
                }
            }
        }
        $data = $this->fread(min($this->chunkLength, $bufferSize));
        $this->chunkLength -= strlen($data);
        if (0 == $this->chunkLength) {
            $this->readLine($bufferSize); // Trailing CRLF
        }
        return $data;
    }
}

?>