<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Model;
ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Modules\IRemNotes\Interfaces\IRemNoteItemInterface;

class IRemNoteItem implements IRemNoteItemInterface
{
    public $markedForDeletion; // : boolean;
    public $noteTitle; // : string
    public $noteDescription; //:string;
    public $noteId;
    public $categoryTags; //IREMNoteItemCategory[];
    public $userId;
    public $created;
    public $lastUpdated;

    public function isMarkedForDeletion(): bool
    {
        return $this->markedFmarkedForDeletionorDeletion;
    }

    public function setMarkedForDeletion(bool $markedForDeletion): IRemNoteItemInterface
    {
        $this->markedForDeletion = $markedForDeletion;
        return $this;
    }

    public function getNoteTitle(): string
    {
        return $this->noteTitle;
    }

    public function setNoteTitle(string $noteTitle): IRemNoteItemInterface
    {
        $this->noteTitle = $noteTitle;
        return $this;
    }

    public function getNoteDescription(): string
    {
        return $this->noteDescription;
    }

    public function setNoteDescription(string $noteDescription): IRemNoteItemInterface
    {
        $this->noteDescription = $noteDescription;
        return $this;
    }

    public function getNoteId(): string
    {
        return $this->noteId;
    }

    public function setNoteId(string $noteId): IRemNoteItemInterface
    {
        $this->noteId = $noteId;
        return $this;
    }

    public function getCategoryTags(): Iterable //IRemNoteItemCategoryInterface[
    {
        return $this->categoryTags;
    }

    public function setCategoryTags(Iterable $categoryTags): IRemNoteItemInterface //IRemNoteItemCategoryInterface[ 
    {
        $this->categoryTags = $categoryTags;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): IRemNoteItemInterface
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function setCreated(string $created): IRemNoteItemInterface
    {
        $this->created = $created;
        return $this;
    }

    public function getLastUpdated(): string
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(string $lastUpdated): IRemNoteItemInterface
    {
        $this->lastUpdated = $lastUpdated;
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
