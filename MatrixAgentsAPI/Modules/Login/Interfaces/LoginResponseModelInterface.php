<?php namespace MatrixAgentsAPI\Modules\Login\Interfaces;

ini_set('display_errors', 1);
error_reporting(E_ALL);
use MatrixAgentsAPI\Shared\Models\Interfaces\MatrixGenericResponseModelInterface;
use MatrixAgentsAPI\Modules\Login\Interfaces\LoginUserRecordInterface;

interface LoginResponseModelInterface extends MatrixGenericResponseModelInterface
{
    public function getUserRecord(): LoginUserRecordInterface;
    public function setUserRecord(LoginUserRecordInterface $userRecord): LoginResponseModelInterface;
    public function setLogs($logs): LoginResponseModelInterface;
    public function getLogs();
    public function getIsAuthenticated();
    public function setIsAuthenticated($isAuthenticated): LoginResponseModelInterface;
    public function getToken();
    public function setToken($token): LoginResponseModelInterface;
    public function getAuthenticatedUserName();
    public function setAuthenticatedUserName($authenticatedUserName): LoginResponseModelInterface;

    public function getJsonString(): string;
}
