<?php

namespace Asad\Zoho\Exception;

class AuthenticationException extends ZohoException
{
    protected $http_status_code = null;

    public function __construct($message, $http_status = null, $exception_json = null)
    {
        $this->http_status_code = $http_status;
        parent::__construct($message, $exception_json);
    }
}
