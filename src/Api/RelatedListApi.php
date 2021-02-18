<?php

namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class RelatedListApi extends RestApi
{
    private $list_extension = [];
    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }
    /**
     * @param string $module Zoho crm Module name
     * @param string $record_id Module record Id
     * @param string $who_related Mentions those
     *
     * @return laravel collection
     */
    public function getRelatedRecords($module, $record_id, $related_list)
    {
        $param['extension'] = $record_id . "/" . $related_list;
        $request = $this->createRequest('get-relatedlist', $module, $param);
        return $this->makeRequest($request);
    }

    /**
     * @param string $module Zoho crm Module name
     * @param string $record_id Module record Id
     * @param string $who_related Mentions those related list
     * @param string $related_record_id
     * @param array  $param for data
     *
     * @return laravel collection
     */

    public function updateRelatedList($module, $record_id, $related_list, $related_record_id, $param = [])
    {
        $param['extension'] = $record_id . "/" . $related_list . "/" . $related_record_id;
        $request = $this->createRequest('update-relatedlist', $module, $param);
        return $this->makeRequest($request);
    }

    public function removeRelatedList($module, $record_id, $related_list, $related_record_id)
    {
        $param['extension'] = $record_id . "/" . $related_list . "/" . $related_record_id;
        $request = $this->createRequest('remove-relatedlist', $module, $param);
        return $this->makeRequest($request);
    }
}
