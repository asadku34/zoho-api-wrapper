<?php

namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class QueryApi extends RestApi
{
    private $list_extension = [];

    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }

    /**
     * @param array $select_query
     *
     * @return array
     */
    public function getRecordsByQuery($select_query, $param = [])
    {
        $request = $this->createRequest('crm-object-query-language', 'coql', $select_query);

        return $this->makeRequest($request);
    }
}
