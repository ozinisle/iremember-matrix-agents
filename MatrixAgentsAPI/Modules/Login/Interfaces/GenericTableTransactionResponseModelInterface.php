<?php namespace MatrixAgentsAPI\Modules\Login\Interfaces;
ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Shared\Models\Interfaces\MatrixGenericResponseModelInterface;

interface GenericTableTransactionResponseModelInterface extends MatrixGenericResponseModelInterface
{
    public function getMatchingRecords();
    public function setMatchingRecords($matchingRecords): GenericTableTransactionResponseModelInterface;

    public function getJson();
    public function getJsonString(): string;
}
