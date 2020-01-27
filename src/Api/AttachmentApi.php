<?php
namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class AttachmentApi extends RestApi
{
    private $attachment_extension = ['Attachments/'];
    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }

    public function getAttachmentList($module, $record_id)
    {
        $param['extension'] = $record_id ."/Attachments";
        $request = $this->createRequest('list-of-attachments', $module, $param);
        return $this->makeRequest($request);
    }

    public function uploadAttachment($module, $record_id, $param)
    {
        $param['extension'] = $record_id ."/Attachments";
        $request = $this->createRequest('upload-attachment', $module, $param);
        return $this->makeRequest($request);
    }

    public function downloadAttachment($module, $record_id, $attachment_id)
    {
        $param['extension'] = $record_id ."/Attachments"."/". $attachment_id;
        $request = $this->createRequest('download-attachment', $module, $param);
        return $this->makeRequest($request);
    }

    public function deleteAttachment($module, $record_id, $attachment_id)
    {
        $param['extension'] = $record_id ."/Attachments"."/". $attachment_id;
        $request = $this->createRequest('delete-attachment', $module, $param);
        return $this->makeRequest($request);
    }

    public function uploaImages($module, $record_id, $param)
    {
        $param['extension'] = $record_id ."/photo";
        $request = $this->createRequest('upload-images', $module, $param);
        return $this->makeRequest($request);
    }

    public function downloadImages($module, $record_id)
    {
        $param['extension'] = $record_id ."/photo";
        $request = $this->createRequest('download-images', $module, $param);
        return $this->makeRequest($request);
    }

    public function deleteImages($module, $record_id, $attachment_id)
    {
        $param['extension'] = $record_id ."/photo";
        $request = $this->createRequest('delete-images', $module, $param);
        return $this->makeRequest($request);
    }

}