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
        if (is_null($module)) {
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

    /**
     * Processing Api request
     */
    public function processRequest($action, $param): array
    {
        $this->parameter = [];
        // Record Api
        if ($action === 'search') {
            foreach ($param as $key => $val) {
                $this->validateParam($key, $val);
                $this->parameter[$key] = $val;
            }
            $this->setActionVerb($action, 'GET');
            $this->URI = str_replace('/?', '?', $this->module . "/" . $this->action . "?" . $this->getQuery());
        }

        if ($action === 'list_of_record') {

            if (isset($param['headers'])) {
                $request_header['headers'] = $param['headers'];
                $this->setDataJson($request_header);
                unset($param['headers']);
            }

            $data_param = isset($param['data']) ? $param['data'] : $param;
            $data_param = (isset($data_param[0]) && is_array($data_param[0])) ? $data_param[0] : $data_param;

            foreach ($data_param as $key => $val) {
                $this->parameter[$key] = $val;
            }

            $this->setActionVerb('Record List', 'GET');
            $this->URI = str_replace('/?', '?', $this->module . "?" . $this->getQuery());
        }

        if ($action == 'specific_record') {
            $this->setActionVerb('Specific Record', 'GET');
            $this->URI = $this->module . "/" . implode('', $param);
        }

        if ($action == 'insert') {
            $this->setActionVerb('Insert', 'POST');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }

        if ($action == 'b-update') {
            $this->setActionVerb('Bulk Update', 'PUT');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }

        if ($action == 'update') {
            $this->setActionVerb('Update', 'PUT');
            $record_id = $param['record_id'];
            unset($param['record_id']);
            $this->setDataJson($param);
            $this->URI = $this->module . "/" . $record_id;
        }

        if ($action == 'upsert') {
            $this->setActionVerb('Upsert', 'POST');
            $this->setDataJson($param);
            $this->URI = $this->module . "/upsert";
        }

        if ($action == 'b-delete') {
            $this->setActionVerb('Bulk Delete', 'DELETE');
            $this->URI = $this->module . "?ids=" . implode(',', $param);
        }

        if ($action == 'delete') {
            $this->setActionVerb('Delete', 'DELETE');
            $this->URI = $this->module . "/" . implode('', $param);
        }

        if ($action == 'deleted') {
            foreach ($param as $key => $val) {
                $this->parameter[$key] = $val;
            }
            $this->setActionVerb('Deleted', 'GET');
            $this->URI = $this->module . "/" . $this->action . "?" . $this->getQuery();
        }

        // Get Module List
        if ($action == 'modules') {
            $this->setActionVerb('All Modules', 'GET');
            $this->URI = implode('', $param) . $this->module;
        }

        //Meta Data Api

        if ($action == 'module-meta') {
            $this->setActionVerb('Module Meta', 'GET');
            $this->URI = implode('', $param) . "modules/" . $this->module;
        }

        if ($action == 'field-meta') {
            $this->setActionVerb('Field Meta', 'GET');
            $this->URI = implode('', $param) . "fields?module=" . $this->module;
        }

        if ($action == 'layout-meta') {
            $this->setActionVerb('Layout Meta', 'GET');
            $this->URI = implode('', $param) . "layouts?module=" . $this->module;
        }

        if ($action == 'layout-meta-id') {
            $this->setActionVerb('Layout Meta By Id', 'GET');
            $layout_id = $param[1];
            $param = $param[0];
            $this->URI = implode('', $param) . "layouts/" . $layout_id . "?module=" . $this->module;
        }

        //Note Api
        if ($action == 'notes-data') {
            $this->setActionVerb('Notes Data', 'GET');
            $this->URI = $this->module;
        }

        if ($action == 'create-notes') {
            $this->setActionVerb('Create Notes', 'POST');
            $this->setDataJson($param);
            $this->URI = $this->module;
        }

        if ($action == 'create-specific-note') {
            $this->setActionVerb('Create Specific Note', 'POST');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $extension . $this->module;
        }

        if ($action == 'get-specific-notes') {
            $this->setActionVerb('Get Specific Notes', 'GET');
            $this->URI = $param['extension'] . $this->module;
        }

        if ($action == 'update-note') {
            $this->setActionVerb('Update Note', 'PUT');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $extension;
        }

        if ($action == 'delete-specific-note') {
            $this->setActionVerb('Delete Specific Note', 'DELETE');
            $this->URI = $param['extension'];
        }

        //User Api
        if ($action == 'user-data') {
            $this->setActionVerb('User Data', 'GET');
            $this->URI = $this->module . '?type=' . $param['type'];
        }

        if ($action == 'user-data-by-id') {
            $this->setActionVerb('Get Specific User', 'GET');
            $this->URI = $this->module . '?type=' . $param['z_user_id'];
        }


        //Tag Api
        if ($action == 'tags-list') {
            $this->setActionVerb('Tag List', 'GET');
            $this->URI = 'settings/tags?module=' . $this->module;
        }

        if ($action == 'create-tags') {
            $this->setActionVerb('Create Tags', 'POST');
            $this->setDataJson($param);
            $this->URI = 'settings/tags?module=' . $this->module;
        }

        if ($action == 'update-tags') {
            $this->setActionVerb('Update Tags', 'PUT');
            $tag_id = $param['z_tag_id'];
            unset($param['z_tag_id']);
            $this->setDataJson($param);
            $this->URI = 'settings/tags/' . $tag_id . '?module=' . $this->module;
        }

        if ($action == 'remove-tags') {
            $this->setActionVerb('Remove Tags', 'DELETE');
            $this->URI = 'settings/tags/' . implode('', $param);
        }

        if ($action == 'create-specific-tags') {
            $this->setActionVerb('Create Specific Tags', 'POST');
            $record_id = $param['z_record_id'];
            unset($param['z_record_id']);
            $this->setDataJson($param);
            $this->URI = $this->module . '/' . $record_id . '/actions/add_tags?tag_names=' . implode(',', $param);
        }

        if ($action == 'remove-specific-tags') {
            $this->setActionVerb('Remove Specific Tags', 'POST');
            $record_id = $param['z_record_id'];
            unset($param['z_record_id']);
            $this->setDataJson($param);
            $this->URI = $this->module . '/' . $record_id . '/actions/remove_tags?tag_names=' . implode(',', $param);
        }

        //Attachments Api
        if ($action == 'list-of-attachments') {
            $this->setActionVerb('List Of Attachments', 'GET');
            $this->URI = $this->module . "/" . $param['extension'];
        }
        if ($action == 'delete-attachment') {
            $this->setActionVerb('Delete Attachment', 'DELETE');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        if ($action == 'download-attachment') {
            $this->setActionVerb('Download Attachment', 'GET');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        if ($action == 'download-images') {
            $this->setActionVerb('Download Images', 'GET');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        if ($action == 'upload-attachment') {
            $this->setActionVerb('Upload Attachment', 'POST');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $this->module . "/" . $extension;
        }

        if ($action == 'upload-images') {
            $this->setActionVerb('Upload Images', 'POST');
            $extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $this->module . "/" . $extension;
        }

        if ($action == 'delete-images') {
            $this->setActionVerb('Delete Images', 'DELETE');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        //RelatedList API
        if ($action == 'get-relatedlist') {
            $this->setActionVerb('Relatedlist Records', 'GET');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        if ($action == 'update-relatedlist') {
            $this->setActionVerb('Update Relatedlist', 'PUT');
            $uri_extension = $param['extension'];
            unset($param['extension']);
            $this->setDataJson($param);
            $this->URI = $this->module . "/" . $uri_extension;
        }

        if ($action == 'remove-relatedlist') {
            $this->setActionVerb('Remove Relatedlist', 'DELETE');
            $this->URI = $this->module . "/" . $param['extension'];
        }

        // CRM Object Query Language
        if ($action == 'crm-object-query-language') {
            $this->setActionVerb('Query Language', 'POST');
            if (isset($param['data'])) {
                $this->setDataJson($param['data']);
            } elseif (isset($param['select_query'])) {
                $this->setDataJson($param);
            } else {
                throw new RequestException('Invalid Query Input');
            }
            $this->URI = $this->module;
        }

        return $this->parameter;
    }

    /**
     * Set both action and http verb
     */
    public function setActionVerb($action, $verb)
    {
        $this->setAction($action);
        $this->setHttpVerb($verb);
    }

    /**
     * Valid email address before make request
     */
    public function validateParam($criteria, $value)
    {
        if (strtolower($criteria) == 'email') {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'code'         => 'IVALID_EMAIL',
                    'details'     => [],
                    'message'     => 'There is no content available for the request.',
                    'status'     => 'error',
                ];
                throw new RequestException('Please input a valid email', json_encode($response));
            }
        }
    }

    public function getQuery(): string
    {
        $param = [];
        foreach ($this->parameter as $key => $value) {
            $param[$key] = (string) $value;
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
