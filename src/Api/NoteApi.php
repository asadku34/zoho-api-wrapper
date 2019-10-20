<?php

namespace Asad\Zoho\Api;

use Asad\Zoho\Abstracts\RestApi;

class NoteApi extends RestApi
{
    private $note_extension = ['settings/'];

    public function __construct($config_id = null)
    {
        parent::__construct($config_id);
    }

    public function getNotes()
    {
        $request = $this->createRequest('notes-data', 'Notes', $this->note_extension);

        return $this->makeRequest($request);
    }

    public function getSpecificNote($module, $id)
    {
        $param['extension'] = $module.'/'.$id.'/';
        $request = $this->createRequest('get-specific-notes', 'Notes', $param);

        return $this->makeRequest($request);
    }

    public function createNotes(array $param)
    {
        $request = $this->createRequest('create-notes', 'Notes', $param);

        return $this->makeRequest($request);
    }

    public function createSpecificNote($module, $parent_id, array $param)
    {
        $param['extension'] = $module.'/'.$parent_id.'/';
        $request = $this->createRequest('create-specific-note', 'Notes', $param);

        return $this->makeRequest($request);
    }

    public function updateNote($module, $parent_id, $note_id, array $param)
    {
        $param['extension'] = $module.'/'.$parent_id.'/'.'Notes'.'/'.$note_id;
        $request = $this->createRequest('update-note', 'Notes', $param);

        return $this->makeRequest($request);
    }

    public function deleteSpecificNote($module, $parent_id, $note_id)
    {
        $param['extension'] = $module.'/'.$parent_id.'/'.'Notes'.'/'.$note_id;
        $request = $this->createRequest('delete-specific-note', 'Notes', $param);

        return $this->makeRequest($request);
    }
}
