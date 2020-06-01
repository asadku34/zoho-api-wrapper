<?php

namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class UserApi extends RestApi
{
    private $moduel = 'users';
    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }

    public function getUsers($type)
    {
        $param['type'] = $type;
        $request = $this->createRequest('user-data', $this->moduel, $param);
        return $this->makeRequest($request);
    }

    public function getUserById($user_id)
    {
        $param['z_user_id'] = $user_id;
        $request = $this->createRequest('user-data-by-id', $this->moduel, $param);
        return $this->makeRequest($request);
    }
}
