<?php

namespace Asad\Zoho\Exception;

use Asad\Zoho\Exception\ZohoException;

class ResponseException extends ZohoException
{

    protected $http_status_code = null;

    public function __construct($message, $http_status = null, $exception_json = null)
    {
        $this->http_status_code = $http_status;
        parent::__construct($message, $exception_json);
    }
}
