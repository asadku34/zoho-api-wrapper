<?php

namespace Asad\Zoho\Client;

use Asad\Zoho\Exception\ResponseException;
use GuzzleHttp\Psr7\Response;

class ZohoResponse
{
    protected $response;

    protected $results;

    protected $status;

    protected $array_response;

    protected $http_status_code;


    /**
     *
     * @param Response $response
     * @param mixed $action
     *
     */
    public function __construct(Response $response, $action = null)
    {
        $this->setResponse($response);

        $this->verifyHttpStatus();

        $this->returnResponse();
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

    protected function verifyHttpStatus()
    {
        $status_code = $this->response->getStatusCode();
        $this->http_status_code = $status_code;

        //Trigger Exception for 204, all 400 series and 500
        if ($status_code == 204) $this->noContentException();

        //Parse Response.
        $this->parseResponse();

        if ($status_code == 202) $this->checkRecordException();

        if ($status_code == 304) {
            $exception_message = 'The requested page has not been modified. In case "If-Modified-Since" header is used for GET APIs';
            $this->noContentException($exception_message);
        }

        if ($status_code >= 400 && $status_code <= 500) $this->throwException($this->results);
    }

    private function noContentException($message = 'There is no content available for the request.')
    {
        $this->setStatus("error");
        $error_response = [
            'code'         => 'NO_CONTENT',
            'details'     => [],
            'message'     => $message,
            'status'     => 'success',
        ];
        throw new ResponseException($message, $this->http_status_code, json_encode($error_response));
    }

    private function checkRecordException()
    {
        $response = $this->results;

        if (isset($response->code)) {
            $this->checkAndTriggerException($response);
        }

        if (isset($response->data)) {
            $response = collect($response->data);
            $response = $response->first();
            $this->checkAndTriggerException($response);
        }
    }

    private function checkAndTriggerException($response)
    {
        if (isset($response->status) && $response->status != 'success') {
            $this->setStatus('error');
            throw new ResponseException($response->message, $this->http_status_code, json_encode($response));
        }
    }

    private function throwException($response)
    {
        $message = isset($response->message) ? $response->message : "Error Occure";
        throw new ResponseException($message, $this->http_status_code, json_encode($response));
    }

    protected function parseResponse()
    {
        $content = $this->response->getBody()->getContents();

        $this->toArray($content);
        $this->results = $this->parseJson($content);
    }

    protected function parseJson($content)
    {
        $content = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ResponseException(sprintf(
                "Failed to parse JSON response: %s",
                json_last_error_msg()
            ));
        }
        return $content;
    }

    public function returnResponse(): ZohoResponse
    {
        $this->setStatus('success');
        return $this;
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
     * @param string $results
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

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return intval($this->http_status_code);
    }
}
