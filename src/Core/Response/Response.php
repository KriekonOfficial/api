<?php

namespace Core\Response;

use Core\Request\Request;
use \GuzzleHttp\Psr7\Response as GuzzleResponse;
use Core\Util\JSONWrapper;
use Core\Response\Exception\ResponseException;

trait Response
{
	protected int $http_code = 200;
	protected string $status = "OK";
	protected array $response = [];
	protected string $message = '';

	private $guzzle_response = null;

	public function getGuzzleResponse() : GuzzleResponse
	{
		if ($this->guzzle_response !== null)
		{
			return $this->guzzle_response;
		}

		$this->setGuzzleResponse();

		return $this->guzzle_response;
	}

	public function setGuzzleResponse(GuzzleResponse $response = null) : void
	{
		if ($response === null)
		{
			$server = Request::getServer();

			$uri = $server->getUri();
			$path = $uri->getPath();

			if ($uri->getQuery() != '')
			{
				$path .= '?' . $uri->getQuery();
			}
			$response = new GuzzleResponse(
				$this->getHttpCode(),
				[
					'Content-Length'                   => strlen($this->json()),
					'Content-Type'                     => 'application/json',
					'Content-Encoding'                 => 'identity',
					'ETag'                             => md5($path . $this->json()),
					'Access-Control-Allow-Origin'      => '*',
					'Access-Control-Allow-Credentials' => 'true',
					'Access-Control-Allow-Methods'     => 'GET, PUT, POST, OPTIONS, DELETE',
					'Access-Control-Allow-Headers'     => 'Accept, Content-Type, Authorization',
					'Accept'                           => 'application/json'
				],
				$this->json(),
				$server->getProtocolVersion());
		}

		$this->guzzle_response = $response;
	}

	public function getStatus() : string
	{
		return $this->status;
	}

	public function setStatus(string $status) : void
	{
		if (!in_array($status, ['OK', 'ERROR', 'RATE_LIMIT']))
		{
			throw new ResponseException('Invalid status');
		}
		$this->status = $status;
	}

	public function getHttpCode() : int
	{
		return $this->http_code;
	}

	public function setHttpCode(int $http_code) : void
	{
		$this->http_code = $http_code;
	}

	public function getResponse() : array
	{
		return $this->response;
	}

	public function setResponse(array $response) : void
	{
		$this->response = $response;
	}

	public function getMessage() : string
	{
		return $this->message;
	}

	public function setMessage(string $message) : void
	{
		$this->message = $message;
	}

	public function __toString() : string
	{
		return $this->json();
	}

	private function json() : string
	{
		return JSONWrapper::json([
			'status' => $this->getStatus(),
			'message' => $this->getMessage(),
			'response' => $this->getResponse()
		]);
	}
}