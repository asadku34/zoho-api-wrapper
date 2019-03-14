<?php
namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;
use Asad\Zoho\Client\ZohoRequest;

class MetadataApi extends RestApi
{
    private $meta_extension = ['settings/'];
    public function __construct($config_id=null)
    {
        parent::__construct($config_id);
    }
    
    public function getModuleMeta($module_name)
    {
        $request = $this->createRequest('module-meta', $module_name, $this->meta_extension);
        return $this->makeRequest($request);
    }
    
    public function getFieldMeta($module_name)
    {
        $request = $this->createRequest('field-meta', $module_name, $this->meta_extension);
        return $this->makeRequest($request);
    }
    
    public function getLayoutMeta($module_name)
    {
        $request = $this->createRequest('layout-meta', $module_name, $this->meta_extension);
        return $this->makeRequest($request);
    }
    
    public function getLayoutMetaById($module_name, $layout_id)
    {
        $param[] = $this->meta_extension;
        $param[] = $layout_id;
        $request = $this->createRequest('layout-meta-id', $module_name, $param);
        return $this->makeRequest($request);
    }


}