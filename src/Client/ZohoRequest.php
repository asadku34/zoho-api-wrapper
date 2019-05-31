<?php

namespace Asad\Zoho\Client;

use Asad\Zoho\Exception\RequestException;
class ZohoRequest
{
    /**
     * Query parameter 
     * */ 
    protected $parameter = null;
    protected $action = null;
    protected $module = null;
    protected $http_verb = null;
    protected $URI = null;
    protected $data_json = null;

    /**
	* ZohoRequest constructor.
	*
	* @param Query
	*/
    public function __construct($action, $module, array $param) 
    {
        $this->setModule($module);
        $this->processRequest($action, $param);
    }

    public function setAction(string $action): ZohoRequest
    {
        $this->action = $action;
        return $this;
    }

    public function getAction(): String
    {
        return $this->action;
    }

    public function setModule(string $module): ZohoRequest
    {
        if(is_null($module)) {
            throw new RequestException("Module Name Missing");
        }
        $this->module = $module;
        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setHttpVerb(string $http_verb): ZohoRequest
    {
        $this->http_verb = $http_verb;
        return $this;
    }

    public function getHttpVerb()
    {
        return $this->http_verb;
    }

    public function processRequest($action, $param): array
    {
        $this->parameter = [];
        if ($action === 'search') {
            foreach ($param as $key => $val) {
                $this->validateParam($key, $val);
                $this->parameter[$key] = $val;
            }
            $this->setAction($action);
            $this->setHttpVerb('GET');
            $this->URI = str_replace('/?', '?', $this->module ."/". $this->action ."?". $this->getQuery());
        }

        if ($action === 'list_of_record') {
            foreach ($param as $key => $val) {
                $this->validateParam($key, $val);
                $this->parameter[$key] = $val;
            }
            $this->setAction('Record List');
            $this->setHttpVerb('GET');
            $this->URI = str_replace('/?', '?', $this->module ."?". $this->getQuery());
        }

        if ($action == 'specific_record') {
            $this->setAction('Specific Record');
            $this->setHttpVerb('GET');
            $this->URI = $this->module ."/". implode('',$param);
        }

        if ($action == 'insert') {
            $this->setAction('Insert');
            $this->setHttpVerb('POST');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }

        if ($action == 'b-update') {
            $this->setAction('Bulk Update');
            $this->setHttpVerb('PUT');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }
        
        if ($action == 'update') {
            $this->setAction('Update');
            $this->setHttpVerb('PUT');
            $record_id = $param['record_id'];
            unset($param['record_id']);
            $this->setDataJson($param);
            $this->URI = $this->module ."/". $record_id;
        }

        if ($action == 'upsert') {
            $this->setAction('Upsert');
            $this->setHttpVerb('POST');
            $this->setDataJson($param);
            $this->URI = $this->module ."/upsert";
        }

        if ($action == 'b-delete') {
            $this->setAction('Bulk Delete');
            $this->setHttpVerb('DELETE');
            $this->URI = $this->module ."?ids=". implode(',', $param); 
        }

        if ($action == 'delete') {
            $this->setAction('Delete');
            $this->setHttpVerb('DELETE');
            $this->URI = $this->module ."/". implode('',$param);
        }

        if ($action == 'deleted') {
            foreach ($param as $key => $val) {
                $this->parameter[$key] = $val;
            }
            $this->setAction('Deleted');
            $this->setHttpVerb('GET');
            $this->URI = $this->module ."/". $this->action ."?". $this->getQuery();
        }

        // Get Module List
        if ($action == 'modules') {
            $this->setAction('All Modules');
            $this->setHttpVerb('GET');
            $this->URI = implode('', $param) . $this->module;
        }

        //Meta Data Processing

        if ($action == 'module-meta') {
            $this->setAction('Module Meta');
            $this->setHttpVerb('GET');
            $this->URI = implode('', $param) ."modules/". $this->module;
        }

        if ($action == 'field-meta') {
            $this->setAction('Field Meta');
            $this->setHttpVerb('GET');
            $this->URI = implode('', $param) ."fields?module=". $this->module;
        }

        if ($action == 'layout-meta') {
            $this->setAction('Layout Meta');
            $this->setHttpVerb('GET');
            $this->URI = implode('', $param) ."layouts?module=". $this->module;
        }

        if ($action == 'layout-meta-id') {
            $this->setAction('Layout Meta By Id');
            $this->setHttpVerb('GET');
            $layout_id = $param[1];
            $param = $param[0];
            $this->URI = implode('', $param) ."layouts/". $layout_id ."?module=". $this->module;
        }

        //Note Api
        if ($action == 'notes-data') {
            $this->setAction('Notes Data');
            $this->setHttpVerb('GET');
            $this->URI = $this->module;
        }

        if ($action == 'create-notes') {
            $this->setAction('Create Notes');
            $this->setHttpVerb('POST');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }

        if ($action == 'create-specific-note') {
            $this->setAction('Create Specific Note');
            $this->setHttpVerb('POST');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $extension . $this->module;
        }

        if ($action == 'get-specific-notes') {
            $this->setAction('Get Specific Notes');
            $this->setHttpVerb('GET');
            $this->URI = $param['extension'] . $this->module;
        }

        if ($action == 'update-note') {
            $this->setAction('Update Note');
            $this->setHttpVerb('PUT');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $extension;
        }

        if ($action == 'delete-specific-note') {
            $this->setAction('Delete Specific Note');
            $this->setHttpVerb("DELETE");
            $this->URI = $param['extension'];
        }

        return $this->parameter;
    }

    public function validateParam($criteria, $value)
    {
        if (strtolower($criteria) == 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'code' 		=> 'IVALID_EMAIL',
                    'details' 	=> [],
                    'message' 	=> 'There is no content available for the request.',
                    'status' 	=> 'error',
                ];
                throw new RequestException('Please input a valid email', json_encode($response));
            }
        }
    }

    public function getQuery(): string
    {
        $param = [];
        foreach ($this->parameter as $key => $value) {
            $param[$key] = (string)$value;
        }
        return (count($param) > 0) ? urldecode(http_build_query($param)) : '';
    }

    public function getURI()
    {
        return $this->URI;
    }

    public function setDataJson($data_json): ZohoRequest
    {
        $this->data_json = $data_json;
        return $this;
    }

    public function getDataJson() 
    {
        return $this->data_json;
    }


}