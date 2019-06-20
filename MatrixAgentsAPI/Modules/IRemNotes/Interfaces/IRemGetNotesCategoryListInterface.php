<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Interfaces;
ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Shared\Models\Interfaces\GenericClassMethodsInterface;

interface IRemGetNotesCategoryListInterface extends GenericClassMethodsInterface
{
    public function getCategoryTagData(): iterable;
    public function setCategoryTagData(iterable $categoryTagData): IRemGetNotesCategoryListInterface;
}
