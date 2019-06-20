<?php namespace MatrixAgentsAPI\Modules\Login\Interfaces;
ini_set('display_errors', 1);
error_reporting(E_ALL);
interface LoginUserRecordInterface
{
    public function getUserId(): string;
    public function setUserId(string $userId): LoginUserRecordInterface;
    public function getUserName(): string;
    public function setUserName(string $userName): LoginUserRecordInterface;
    public function getPassword(): string;
    public function setPassword(string $password): LoginUserRecordInterface;
    public function getUserRole(): string;
    public function setUserRole(string $userRole): LoginUserRecordInterface;
}
