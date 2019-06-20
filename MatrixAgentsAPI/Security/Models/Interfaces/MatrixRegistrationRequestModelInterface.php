<?php namespace MatrixAgentsAPI\Security\Models\Interfaces;

ini_set('display_errors', 1);
error_reporting(E_ALL);


interface MatrixRegistrationRequestModelInterface
{
    public function getUsername(): string;
    public function setUsername(string $username): MatrixRegistrationRequestModelInterface;
    public function getPassword(): string;
    public function setPassword(string $password): MatrixRegistrationRequestModelInterface;
    public function getFirstname(): string;
    public function setFirstname(string $firstname): MatrixRegistrationRequestModelInterface;
    public function getLastname(): string;
    public function setLastname(string $lastname): MatrixRegistrationRequestModelInterface;
    public function getEmail(): string;
    public function setEmail(string $email): MatrixRegistrationRequestModelInterface;
    public function getMobile(): string;
    public function setMobile(string $mobile): MatrixRegistrationRequestModelInterface;
}
