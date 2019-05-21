<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Interfaces;

use MatrixAgentsAPI\Shared\Models\Interfaces\GenericClassMethodsInterface;

interface IRemGetNotesListInterface extends GenericClassMethodsInterface
{
    public function getNotesList(): iterable;
    public function setNotesList(iterable $notesList): IRemGetNotesListInterface;
}
