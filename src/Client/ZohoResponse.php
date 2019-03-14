<?php

namespace Asad\Zoho\Client;

use Asad\Zoho\Exception\ResponseException;
use GuzzleHttp\Psr7\Response;

use Asad\Zoho\HttpStatus\HttpStatus;

class ZohoResponse
{	
  	protected $response = null;

    protected $results = null;

    protected $status = null;

    protected $error_message = null;

    protected $array_response = null;

	protected $http_status_code = null;


    /**sss
	* ZohoResponse constructor.
	*
	* @param Response $response
	*/
    public function __construct(Response $response) 
    {
		$this->setResponse($response);
		$this->checkHttpStatusCode();
		$this->parseResponse();
    }
    
    /**
	 * @param Response $response
	 *
	 * @return ZohoResponse
	 */
	public function setResponse(Response $response): ZohoResponse 
	{
		$this->response = $response;
		return $this;
	}

		
	/**
	 * @return ZohoResponse
	 *
	 * @throws ResponseException
	 */
	protected function parseResponse(): ZohoResponse 
	{
		$json_response = $this->response->getBody()->getContents();

		if ($this->http_status_code === 204) {
			$this->setStatus("error");
			$error_response = [
				'status' => 'NO_CONTENT',
				'data' => 'There is no content available for the request.'
			];
			throw new ResponseException("There is no content available for the request.", $this->http_status_code, json_encode($error_response));
		}
		
		if ($this->http_status_code !== 200 && 
			$this->http_status_code !== 201 && 
			$this->http_status_code !== 202
			) {
			$error_response = json_decode($json_response);
			$this->setStatus('error');
			throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
		}

		$this->setResults($json_response);
		$array_response = $this->toArray($json_response);
		$this->setStatus('success');
		
		return $this;
	}

	/**
	 * Check HTTP status code (silent/No exceptions!)
	 * @return int
	 */
	protected function checkHttpStatusCode(): int 
	{
		$this->http_status_code = $this->response->getStatusCode();
		return $this->http_status_code;
	}
	/**
	 * @param string $json_response
	 *
	 * @return array
	 */
	public function toArray(string $json_response): array 
	{
		$this->array_response = json_decode($json_response, true);
		return $this->array_response;
	}
		
	/**
	 * @return array
	 */
	public function getResults() 
	{
		return $this->results;
	}
	/**
	 * @param array $results
	 *
	 * @return $this
	 */
	public function setResults($results) 
	{
		$this->results = json_decode($results);
		return $this;
	}
	/**
	 * @return string
	 */
	public function getStatus(): string 
	{
		return $this->status;
	}
	/**
	 * @param string $status
	 *
	 * @return ZohoRespone
	 */
	public function setStatus(string $status): ZohoResponse 
	{
		$this->status = $status;
		return $this;
	}
	/**
	 * @return array
	 */
	public function getArrayResponse(): array 
	{
		return $this->array_response;
	}
	/**
	 * @param array $array_response
	 *
	 * @return ZohoResponse
	 */
	public function setArrayResponse(array $array_response): ZohoResponse 
	{
		$this->array_response = $array_response;
		return $this;
	}
	/**
	 * @return mixed
	 */
	public function getErrorMessage() 
	{
		return $this->error_message;
	}
	/**
	 * @param $error_message
	 *
	 * @return ZohoResponse
	 */
	public function setErrorMessage($error_message): ZohoResponse 
	{
		$this->error_message = $error_message;
		return $this;
	}
	/**
	 * @return int
	 */
	public function getHttpStatusCode(): int 
	{
		return intval($this->http_status_code);
	}


}