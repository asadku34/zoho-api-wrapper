<?php

namespace Asad\Zoho\HttpStatus;

class HttpStatus
{
    private static $http_status = [
        '200' => [
            'status' => 'OK',
            'data' => 'The API request is successful.'
        ],
        '204' => [
            'status' => 'NO CONTENT',
            'data' => 'There is no content available for the request.'
        ],
        '400' => [
            'status' => 'BAD REQUEST',
            'data' => 'The request or the authentication considered is invalid.'
        ],
        '401' => [
            'status' => 'AUTHORIZATION ERROR',
            'data' => 'Invalid API key provided.'
        ],
    ];

    private static $status = [
        200 => 'success',
        201 => 'no_content',
    ];

    public static function getHttpStatus($http_code)
    {
        return isset(self::$http_status[$http_code]) ? self::$http_status[$http_code] : false;
    }
}