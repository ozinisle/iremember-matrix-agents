<?php namespace MatrixAgentsAPI\Modules\IRemNotes\Interfaces;

use MatrixAgentsAPI\Shared\Models\Interfaces\GenericClassMethodsInterface;

interface IRemGetNotesCategoryListInterface extends GenericClassMethodsInterface
{
    public function getCategoryTagData(): iterable;
    public function setCategoryTagData(iterable $categoryTagData): IRemGetNotesCategoryListInterface;
}
