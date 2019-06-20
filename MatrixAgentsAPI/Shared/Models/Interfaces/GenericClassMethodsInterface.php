<?php namespace MatrixAgentsAPI\Shared\Models\Interfaces;

ini_set('display_errors', 1);
error_reporting(E_ALL);


interface GenericClassMethodsInterface
{
    public function getJson();
    public function getJsonString(): string;
}
