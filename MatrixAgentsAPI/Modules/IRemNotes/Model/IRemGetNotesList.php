<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Model;

use MatrixAgentsAPI\Modules\IRemNotes\Interfaces\IRemGetNotesListInterface;

class IRemGetNotesList implements IRemGetNotesListInterface
{
    public $notesList;

    public function getNotesList(): iterable
    {
        return $this->notesList;
    }

    public function setNotesList(iterable $notesList): IRemGetNotesListInterface
    {
        $this->notesList = $notesList;
        return $this;
    }

    public function getJson()
    {
        //returns the json equivalent of the current class object
        // $json = get_object_vars($this);
        // $json['categoryTags'] = $this->categoryTags->getJson();
        // return $json;

        return get_object_vars($this);
    }

    public function getJsonString(): string
    {
        //returns the json string equivalent of the current class object
        return json_encode(get_object_vars($this));
    }
}
