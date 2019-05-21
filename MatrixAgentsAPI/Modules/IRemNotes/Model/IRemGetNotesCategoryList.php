<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Model;

use MatrixAgentsAPI\Modules\IRemNotes\Interfaces\IRemGetNotesCategoryListInterface;

class IRemGetNotesCategoryList implements IRemGetNotesCategoryListInterface
{
    public $categoryTagData;

    public function getCategoryTagData(): iterable
    {
        return $this->categoryTagData;
    }

    public function setCategoryTagData(iterable $categoryTagData): IRemGetNotesCategoryListInterface
    {
        $this->categoryTagData = $categoryTagData;
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
