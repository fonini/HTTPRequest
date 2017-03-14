<?php

/**
 * HTTPRequest class.
 *
 * This class retrieves remote content on servers without cURL and/or 
 * other limitations like allow_url_fopen using sockets. It also validates 
 * the URL passed.
 *
 * @author Jonnas Fonini <jonnasfonini@gmail.com>
 * @version 2011-02-27
 */
class HTTPRequest{
	
	private $url;
	private $protocol;
	private $host;
	private $port;
	private $uri;
	private $header;
	private $content;
	private $socket;

	private $regex = '#^(https?)://(([a-z0-9-]+\.)+[a-z]{2,6}|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):?([0-9]+)?(/?|/\S+)$#ix';
	private $crlf = "\r\n";

	/**
 	 * Initializes this HTTPRequest
	 *
	 * @param string $url  The request url
	 *
	 * @throws <b>Exception</b> If the URL passed is invalid
	 */
	public function __construct($url){
		if (preg_match($this->regex, $url, $result) == 0){
			throw new Exception('Invalid URL: '.$url);	
		}

		$this->url = $result[0];
		$this->protocol = $result[1];
		$this->host = $result[2];
		$this->port = $result[4];
		$this->port = empty($result[4]) ? (($this->protocol == 'https') ? 443: 80) : $result[4];
		$this->uri = empty($result[5]) ? '/' : $result[5];

		$this->parseResponse();
	}

	/**
	 * Parse the response, spliting headers and body.
	 *
	 */
	private function parseResponse(){
		$response = $this->makeRequest();

		// Split content and response headers
		$position = strpos($response, $this->crlf.$this->crlf);
		$header = substr($response, 0, $position);
		$this->content = substr($response, $position + 2 * strlen($this->crlf));
 
		// Parse response headers
		$this->header = array();
		$lines = explode($this->crlf, $header);
		foreach($lines as $line){
			if(($position = strpos($line, ':')) !== false){
				$this->header[substr($line, 0, $position)] = trim(substr($line, $position++));
			}
		}
	}

	/**
	 * Open the socket connection and retrieves the content.
	 *
	 * @return string Response
	 *
	 * @throws <b>Exception</b> If the socket connection fails
	 */
	private function makeRequest(){
		$response = '';
		$request = 'GET '.$this->uri.' HTTP/1.0'.$this->crlf
			.'Host: '.$this->host.$this->crlf
			.$this->crlf;

		if (!$this->socket = fsockopen(($this->protocol == 'https' ? 'ssl://' : '').$this->host, $this->port, $errno, $errstr)){
			throw new Exception('Connection could not be established with host '.$this->host.
				' ['.$errstr.' #'.$errno.']'
			);
		}

		fwrite($this->socket, $request);
		while(is_resource($this->socket) && !feof($this->socket)){
			$response .= fread($this->socket, 1024);
		}
		fclose($this->socket);

		return $response;
	}

	/**
	 * Retrieves the response header from the request.
	 *
	 * @return array Header
	 */
	public function getHeader(){
		return $this->header;
	}

	/**
	 * Retrieves the response body from the request
	 *
	 * @return string Content
	 */
	public function getContent(){
		return $this->content;
	}

	public function __toString(){
		return $this->getContent();
	}
}
