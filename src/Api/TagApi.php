<?php
namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class TagApi extends RestApi
{
    private $tag_extension = [];
    public function __construct($config_id=null)
    {
        parent::__construct($config_id);
    }

    public function getTagList($module)
    {
        $request = $this->createRequest('tags-list', $module, $this->tag_extension);
        return $this->makeRequest($request);
    }

    public function createTags($module, array $param)
    {
        $request = $this->createRequest('create-tags', $module, $param);
        return $this->makeRequest($request);
    }

    public function updateTags($module, $tag_id, array $param)
    {
        $param['z_tag_id'] = $tag_id;
        $request = $this->createRequest('update-tags', $module, $param);
        return $this->makeRequest($request);
    }

    public function removeTags($tag_id)
    {
        $request = $this->createRequest('remove-tags', 'tags', [$tag_id]);
        return $this->makeRequest($request);
    }

    public function createSpecificRecordTags($module, $record_id, array $param)
    {
        $param['z_record_id'] = $record_id;
        $request = $this->createRequest('create-specific-tags', $module, $param);
        return $this->makeRequest($request);
    }

    public function removeSpecificRecordTags($module, $record_id, array $param)
    {
        $param['z_record_id'] = $record_id;
        $request = $this->createRequest('remove-specific-tags', $module, $param);
        return $this->makeRequest($request);
    }

}