<?php

namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class RecordApi extends RestApi
{
    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }

    /**
     * @param $module = 'Leads'
     */
    public function listOfRecords($module, array $param = [])
    {
        $request = $this->createRequest('list_of_record', $module, $param);

        return $this->makeRequest($request);
    }

    /**
     * @param string $module    = 'Leads'
     * @param string $record_id = '1108640000047708962'
     */
    public function recordById($module, $record_id)
    {
        $request = $this->createRequest('specific_record', $module, [$record_id]);

        return $this->makeRequest($request);
    }

    /**
     * @param string $module = 'Lead'
     * @param array  $param  = ['email' => 'test@gmail.com']
     */
    public function search($module, array $param)
    {
        $request = $this->createRequest('search', $module, $param);

        return $this->makeRequest($request);
    }

    public function insert($module, array $param)
    {
        $request = $this->createRequest('insert', $module, $param);

        return $this->makeRequest($request);
    }

    public function bulkUpdate($module, array $param)
    {
        $request = $this->createRequest('b-update', $module, $param);

        return $this->makeRequest($request);
    }

    public function update($module, $record_id, array $param)
    {
        $param['record_id'] = $record_id;
        $request = $this->createRequest('update', $module, $param);

        return $this->makeRequest($request);
    }

    public function upsert($module, array $param)
    {
        $request = $this->createRequest('upsert', $module, $param);

        return $this->makeRequest($request);
    }

    public function bulkDelete($module, array $param)
    {
        $request = $this->createRequest('b-delete', $module, $param);

        return $this->makeRequest($request);
    }

    public function delete($module, string $record_id)
    {
        $request = $this->createRequest('delete', $module, [$record_id]);

        return $this->makeRequest($request);
    }

    public function getDeletedRecord($module, $param)
    {
        //TODO: Need To investigate
        dd('STOP');
        $request = $this->createRequest('deleted', $module, $param);

        return $this->makeRequest($request);
    }

    public function convert()
    {
        //TODO:: Need to Implement
        echo 'Need to implement';
    }
}
