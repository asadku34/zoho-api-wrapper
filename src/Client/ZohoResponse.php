<?php

namespace Asad\Zoho\Client;

use Asad\Zoho\Exception\ResponseException;
use GuzzleHttp\Psr7\Response;

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
    public function __construct(Response $response, $action) 
    {
		$this->setResponse($response);
		$this->checkHttpStatusCode();
		$this->parseResponse($action);
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
	protected function parseResponse($action): ZohoResponse
	{
		
		$json_response = $this->response->getBody()->getContents();
		$invoke_function = camel_case(str_replace(' ', '', $action));
		return $this->$invoke_function($json_response);

	}

	private function setSuccessResponse($json_response)
	{
		$this->setResults($json_response);
		$array_response = $this->toArray($json_response);
		$this->setStatus('success');
		return $this;
	}

	private function yieldException($json_response)
	{
		if ($this->http_status_code == 500) {
			$this->internalServerException($json_response);
		}

		$error_response = json_decode($json_response);
		$this->setStatus('error');
		throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
	}

	private function internalServerException($json_response)
	{
		$error_response = json_decode($json_response);
		$this->setStatus('error');
		throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
	}

	private function noContentException($message = 'There is no content available for the request.')
	{
		$this->setStatus("error");
		$error_response = [
			'code' 		=> 'NO_CONTENT',
			'details' 	=> [],
			'message' 	=> $message,
			'status' 	=> 'success',
		];
		throw new ResponseException($message, $this->http_status_code, json_encode($error_response));
	}

	private function insertException($json_response)
	{
		$error_response = json_decode($json_response);
		$error_response = collect($error_response->data);
		$error_response = $error_response->first();
		$this->setStatus('error');
		throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
	}

	private function updateException($json_response)
	{
		$error_response = json_decode($json_response);
		$error_response = collect($error_response->data);
		$error_response = $error_response->first();
		$this->setStatus('error');
		throw new ResponseException("Failed to update one/more records.", $this->http_status_code, json_encode($error_response));
	}
	private function deleteException($json_response)
	{
		$error_response = json_decode($json_response);
		$error_response = collect($error_response->data);
		$error_response = $error_response->first();
		$this->setStatus('error');
		throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
	}

	/**
	 * Parse response
	 */
	private function refreshToken($json_response)
	{
		return $this->setSuccessResponse($json_response);
	}

	private function get($json_response)
	{
		return $this->recordResponse($json_response);
	}

	private function post($json_response)
	{
		return $this->recordResponse($json_response);
	}

	//Record Response
	private function recordResponse($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		$this->yieldException($json_response);
	}

	private function search($json_response)
	{
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		return $this->recordResponse($json_response);
	}

	private function recordList($json_response)
	{
		if ($this->http_status_code == 304) {
			$exception_message = 'The requested page has not been modified. In case "If-Modified-Since" header is used for GET APIs';
			$this->noContentException($exception_message);
		}
		return $this->search($json_response);
	}

	private function specificRecord($json_response)
	{
		return $this->search($json_response);
	}

	private function insert($json_response)
	{
		if ($this->http_status_code == 201) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 400) {
			$this->yieldException($json_response);
		}

		if ($this->http_status_code == 403) {
			$this->yieldException($json_response);
		}

		$this->insertException($json_response);
	}

	private function update($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 202) {
			$this->updateException($json_response);
		}

		$this->yieldException($json_response);

	}

	private function bulkUpdate($json_response)
	{
		return $this->update($json_response);
	}

	private function upsert($json_response)
	{
		return $this->update($json_response);
	}

	private function delete($json_response)
	{ 
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 400) {
			$this->yieldException($json_response);
		}

		$this->deleteException($json_response);
	}

	private function bulkDelete($json_response)
	{
		return $this->delete($json_response);
	}

	//Process Meta Data Response
	private function metaResponse($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}

		$this->yieldException($json_response);
	}

	private function allModules($json_response)
	{
		return $this->metaResponse($json_response);
	}

	private function moduleMeta($json_response)
	{
		return $this->metaResponse($json_response);
	}

	private function fieldMeta($json_response)
	{
		return $this->metaResponse($json_response);
	}

	private function layoutMeta($json_response)
	{
		return $this->metaResponse($json_response);
	}

	private function layoutMetaById($json_response)
	{
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		return $this->metaResponse($json_response);
	}

	//Process Notes api
	private function noteResponse($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		$this->yieldException($json_response);
	}

	private function deleteNoteException($json_response)
	{
		$error_response = json_decode($json_response);
		$error_response = collect($error_response->data);
		$error_response = $error_response->first();
		$this->setStatus('error');
		throw new ResponseException($error_response->message, $this->http_status_code, json_encode($error_response));
	}

	private function notesData($json_response)
	{
		if ($this->http_status_code == 204) {
			return $this->noContentException();
		}
		return $this->noteResponse($json_response);
	}

	private function getSpecificNotes($json_response)
	{
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		return $this->noteResponse($json_response);
	}

	private function createNotes($json_response)
	{
		
		if ($this->http_status_code == 201) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 400) {
			$this->yieldException($json_response);
		}

		if ($this->http_status_code == 403) {
			$this->yieldException($json_response);
		}

		$this->insertException($json_response);
	}

	private function createSpecificNote($json_response)
	{
		return $this->createNotes($json_response);
	}

	private function updateNote($json_response)
	{

		if ($this->http_status_code == 202) {
			return $this->updateException($json_response);
		}

		$this->noteResponse($json_response);
	}

	private function deleteSpecificNote($json_response)
	{
		if ($this->http_status_code == 200) {
			$delete_response = json_decode($json_response);
			$delete_response = collect($delete_response->data);
			$delete_response = $delete_response->first();
			if ($delete_response->status == 'error') {
				$this->http_status_code = 400;
				$this->deleteNoteException($json_response);
			}
			return $this->noteResponse($json_response);
		}

		$this->yieldException($json_response);
	}

	//Process Tag Api
	private function tagResponse($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		$this->yieldException($json_response);
	}

	private function tagList($json_response)
	{
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		return $this->tagResponse($json_response);
	}

	private function createTags($json_response)
	{
		
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 201) {
			return $this->setSuccessResponse($json_response);
		}

		$this->yieldException($json_response);
	}

	private function updateTags($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 201) {
			return $this->setSuccessResponse($json_response);
		}

		$this->yieldException($json_response);
	}

	private function removeTags($json_response)
	{
		
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 201) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 400) {
			return $this->yieldException($json_response);
		}

		$this->yieldException($json_response);
	}

	private function createSpecificTags($json_response)
	{
		return $this->createTags($json_response);
	}

	private function removeSpecificTags($json_response)
	{
		return $this->createTags($json_response);
	}

	//Process attachments api
	private function listOfAttachments($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function uploadAttachment($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}
	
	private function deleteAttachment($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function downloadAttachment($json_response)
	{
		if ($this->http_status_code == 200) {
			$this->results = $json_response;
			$this->setStatus('success');
			return $this;
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function uploadImages($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}
		
		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function downloadImages($json_response)
	{
		if ($this->http_status_code == 200) {
			$this->results = $json_response;
			$this->setStatus('success');
			return $this;
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function deleteImages($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	//Process relatedlist api
	private function relatedlistRecords($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
	}

	private function updateRelatedlist($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 202) {
			$this->updateException($json_response);
		}
		$this->yieldException($json_response);
	}

	private function removeRelatedlist($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 202) {
			$this->updateException($json_response);
		}
		$this->yieldException($json_response);
	}
	
	private function queryLanguage($json_response)
	{
		if ($this->http_status_code == 200) {
			return $this->setSuccessResponse($json_response);
		}

		if ($this->http_status_code == 204) {
			$this->noContentException();
		}
		$this->yieldException($json_response);
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