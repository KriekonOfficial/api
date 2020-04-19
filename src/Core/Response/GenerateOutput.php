<?php

namespace Core\Response;

use Core\Response\Interfaces\ResponseInterface;

class GenerateOutput
{
	private $response;

	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
	}

	public function getResponse() : ResponseInterface
	{
		return $this->response;
	}

	public function output() : string
	{
		$guzzle = $this->getResponse()->getGuzzleResponse();

		foreach ($guzzle->getHeaders() as $header => $value)
		{
			header($header .': ' . $guzzle->getHeaderLine($header));
		}

		header('HTTP/' . $guzzle->getProtocolVersion() . ' ' . $guzzle->getStatusCode() . ' ' . $guzzle->getReasonPhrase());

		return (string)$guzzle->getBody();
	}
}