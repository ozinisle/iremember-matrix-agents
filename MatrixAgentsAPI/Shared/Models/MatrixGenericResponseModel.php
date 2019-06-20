<?php namespace MatrixAgentsAPI\Shared\Models;

ini_set('display_errors', 1);
error_reporting(E_ALL);


use MatrixAgentsAPI\Shared\Models\Interfaces\MatrixGenericResponseModelInterface;

class MatrixGenericResponseModel implements MatrixGenericResponseModelInterface
{
    public $status; // SUCCESS or FAILURE
    public $errorMessage;
    public $displayMessage;
    public $responseCode;

    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    public function setResponseCode(string $responseCode): MatrixGenericResponseModelInterface
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): MatrixGenericResponseModelInterface
    {
        $this->status = $status;
        return $this;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): MatrixGenericResponseModelInterface
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getDisplayMessage(): string
    {
        return $this->displayMessage;
    }

    public function setDisplayMessage(string $displayMessage): MatrixGenericResponseModelInterface
    {
        $this->displayMessage = $displayMessage;
        return $this;
    }

    public function getJson()
    {
        //returns the json equivalent of the current class object
        return get_object_vars($this);
    }

    public function getJsonString(): string
    {
        //returns the json string equivalent of the current class object
        return json_encode(get_object_vars($this));
    }
}
