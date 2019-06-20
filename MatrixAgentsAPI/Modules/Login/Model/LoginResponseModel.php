<?php namespace MatrixAgentsAPI\Modules\Login\Model;

ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Modules\Login\Interfaces\LoginResponseModelInterface;
use MatrixAgentsAPI\Shared\Models\MatrixGenericResponseModel;
use MatrixAgentsAPI\Modules\Login\Interfaces\LoginUserRecordInterface;

class LoginResponseModel extends MatrixGenericResponseModel implements LoginResponseModelInterface
{

    public $userRecord; //: LoginUserRecord
    public $logs = array();
    public $isAuthenticated = false;
    public $token;
    public $authenticatedUserName;

    public function getIsAuthenticated()
    {
        return $this->isAuthenticated;
    }

    public function setIsAuthenticated($isAuthenticated): LoginResponseModelInterface
    {
        $this->isAuthenticated = $isAuthenticated;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token): LoginResponseModelInterface
    {
        $this->token = $token;
        return $this;
    }

    public function getAuthenticatedUserName()
    {
        return $this->token;
    }

    public function setAuthenticatedUserName($authenticatedUserName): LoginResponseModelInterface
    {
        $this->authenticatedUserName = $authenticatedUserName;
        return $this;
    }

    public function getUserRecord(): LoginUserRecordInterface
    {
        return $this->userRecord;
    }

    public function setUserRecord(LoginUserRecordInterface $userRecord): LoginResponseModelInterface
    {
        $this->userRecord = $userRecord;
        return $this;
    }

    public function getLogs()
    {
        return $this->logs;
    }

    public function setLogs($logs): LoginResponseModelInterface
    {
        $this->logs = $logs;
        return $this;
    }

    public function getJsonString(): string
    {
        //returns the json string equivalent of the current class object
        return json_encode(get_object_vars($this));
    }
}
