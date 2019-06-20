<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Interfaces;
ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Shared\Models\Interfaces\GenericClassMethodsInterface;

interface IRemGetNotesListInterface extends GenericClassMethodsInterface
{
    public function getNotesList(): iterable;
    public function setNotesList(iterable $notesList): IRemGetNotesListInterface;
}
