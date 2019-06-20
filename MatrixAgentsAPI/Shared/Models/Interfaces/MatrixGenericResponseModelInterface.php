<?php namespace MatrixAgentsAPI\Shared\Models\Interfaces;

ini_set('display_errors', 1);
error_reporting(E_ALL);


use MatrixAgentsAPI\Shared\Models\Interfaces\GenericClassMethodsInterface;

interface MatrixGenericResponseModelInterface extends GenericClassMethodsInterface
{
    function getStatus(): string;
    function setStatus(string $status): MatrixGenericResponseModelInterface;
    function getErrorMessage(): string;
    function setErrorMessage(string $errorMessage): MatrixGenericResponseModelInterface;
    function getDisplayMessage(): string;
    function setDisplayMessage(string $displayMessage): MatrixGenericResponseModelInterface;
    function getResponseCode(): string;
    function setResponseCode(string $responseCode): MatrixGenericResponseModelInterface;
}
