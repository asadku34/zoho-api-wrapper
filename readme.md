# ZOHO CRM API V2 Wrapper for Laravel 5.x

[![Laravel 5.x](https://img.shields.io/badge/Laravel-5.x-orange.svg)](http://laravel.com)
[![Latest Stable Version](https://img.shields.io/packagist/v/asad/laravel-zoho-api-wrapper.svg)](https://packagist.org/packages/asad/laravel-zoho-api-wrapper)
[![Latest Unstable Version](https://poser.pugx.org/asad/laravel-zoho-api-wrapper/v/unstable.svg)](https://packagist.org/packages/asad/laravel-zoho-api-wrapper)
[![Total Downloads](https://poser.pugx.org/asad/laravel-zoho-api-wrapper/downloads.png)](https://packagist.org/packages/asad/laravel-zoho-api-wrapper)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/asad/laravel-zoho-api-wrapper)

To easing [ZOHO](https://www.zoho.com/crm/developer/docs/api/overview.html) REST API V2 call for Laravel application.

## Requirements
- [PHP >= 7.0](http://php.net/)
- [Laravel 5.4|5.5|5.6|5.7](https://github.com/laravel/framework)

## Installation
```bash
$ composer require asad/laravel-zoho-api-wrapper
```

#### Configuration
```bash
$ php artisan zoho:authentication

Input CRM client id:
> (your client id)
Input CRM client secret:
> (your client secret)
Input client domain (ex: example.com):
> (your client domain name)
Select your protocol.[http]:
[0] http
[1] https
> 0
Select your env. [sandbox]:
[0] sandbox
[1] live
> 0

Copy the following url, paste on browser and hit return.
https://accounts.zoho.com/oauth/v2/auth?....

```

## How to use (example)

```php
> ZohoController.php

use Asad\Zoho\Api\RecordApi;
use Asad\Zoho\Exception\ZohoException;

Class ZohoController extends Controller
{

    public function testAPI()
    {
        $param = [
            'headers' => [
                'If-Modified-Since' => date('c')
            ],
            'data' => [
                'page' => 1,
                'per_page' => 20,
            ]
        ];

        $response = $this->listOfRecords('Leads', $param);
        dd($response);
    }

    public function listOfRecords($module, array $param)
    {   
        $record_api = new RecordApi();
        try {
            $response = $record_api->listOfRecords($module, $param);
            if ($response->getStatus() == 'success') {
                $crm_data = $response->getResults();
                return $crm_data;
            }
        } catch(ZohoException $e) {
            // Handle Exception and return
        }
    }
}

```

## APIs
Currently there are only 6 api wrappers.
Those are follows:
- Record APIs
- Meta Data APIs (It also belong Module API)
- Note APIs
- Related List APIs
- Tag APIs
- Attachments APIs

## Contributing

Contributions are **welcome** and will be fully **credited**.

I accept contributions via Pull Requests on [Github](https://github.com/asadku34/zoho-api-wrapper/pulls).

## Issues

If you discover any issues, please email at [asadku34@gmail.com](mailto:asadku34@gmail.com) also you can create issue on the issue tracker.

## Credits

- [Asadur Rahman](https://github.com/asadku34)

## License

The MIT License (MIT). Please see [License File](https://github.com/asadku34/zoho-api-wrapper/blob/master/LICENSE) for more information.