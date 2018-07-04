<?php namespace Cartalyst\SentrySocial\HttpClients;
/**
 * Part of the Sentry Social package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Client implementation for streams/file_get_contents
 */
class StreamClient implements ClientInterface
{
	/**
	 * Maximum redirects.
	 *
	 * @var int
	 */
	protected $maxRedirects;

	/**
	 * Timeout.
	 *
	 * @var int
	 */
	protected $timeout;

	/**
	 * @param int $maxRedirects Maximum redirects for client
	 * @param int $timeout Request timeout time for client in seconds
	 */
	public function __construct($maxRedirects = 5, $timeout = 15)
	{
		$this->maxRedirects = $maxRedirects;
		$this->timeout      = $timeout;
	}

	/**
	 * Retrieves a response from the given endpont.
	 *
	 * @param  OAuth\Common\Http\Uri\UriInterface $endpoint
	 * @param  mixed   $requestBody
	 * @param  array   $extraHeaders
	 * @param  string  $method
	 * @return string
	 * @throws OAuth\Common\Http\Exception\TokenResponseException
	 * @throws InvalidArgumentException
	 */
	public function retrieveResponse(UriInterface $endpoint, $requestBody, array $extraHeaders = array(), $method = 'POST')
	{
		// Normalize method name
		$method = strtoupper($method);

		// Normalize headers
		array_walk($extraHeaders, function(&$val, &$key)
		{
			$key = ucfirst( strtolower($key) );
			$val = ucfirst( strtolower($key) ) . ': ' . $val;
		});


		if ($method === 'GET' and ! empty($requestBody))
		{
			throw new \InvalidArgumentException('No body expected for "GET" request.');
		}

		if ( ! isset($extraHeaders['Content-type']) and $method === 'POST' and is_array($requestBody))
		{
			$extraHeaders['Content-type'] = 'Content-type: application/x-www-form-urlencoded';
		}

		$extraHeaders['Host'] = 'Host: '.$endpoint->getHost();
		$extraHeaders['Connection'] = 'Connection: close';

		if (is_array($requestBody))
		{
			$requestBody = http_build_query($requestBody);
		}

		$context = $this->generateStreamContext($requestBody, $extraHeaders, $method);

		$level = error_reporting(0);
		$response = file_get_contents($endpoint->getAbsoluteUri(), 0, $context);
		error_reporting($level);

		if (false === $response)
		{
			$lastEror = error_get_last();
			throw new TokenResponseException($lastEror['message']);
		}

		return $response;
	}

	/**
	 * Generates a stream context with the given values.
	 *
	 * @param  string  $body
	 * @param  array   $headers
	 * @param  stirng  $method
	 * @return resource
	 */
	protected function generateStreamContext($body, $headers, $method)
	{
		return stream_context_create(array(
			'http' => array(
				'method'           => $method,
				'header'           => implode("\r\n", $headers)."\r\n\r\n",
				'content'          => $body,
				'protocol_version' => '1.1',
				'user_agent'       => 'SentrySocial',
				'max_redirects'    => $this->maxRedirects,
				'timeout'          => $this->timeout,
			),
		));
	}
}
