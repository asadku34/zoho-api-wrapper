<?php

namespace Asad\Zoho\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\UriInterface;

use Exception;

class ZohoException extends Exception
{
    protected $message = null;
    protected $exception_json = null;
    public function __construct($message, $exception_json = null) {
        $this->message = $message;
        $this->exception_json = $exception_json;
        parent::__construct($message);
    }

    public function getResponse()
    {
        if ($this->exception_json !== null) {
            $this->exception_json = json_decode($this->exception_json, true);
            return $this;
        }
        return $this;
    }

    /**
	 * @return int
	 */
	public function getHttpStatusCode(): int 
	{
		return intval($this->http_status_code);
    }

    /** 
     * @return JSON 
     */
    
    public function getExceptionJson()
    {
        return $this->exception_json;
    }

    public function hasResponse()
    {
        return $this->exception_json !== null;
    }
}