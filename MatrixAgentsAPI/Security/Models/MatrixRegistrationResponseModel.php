<?php namespace MatrixAgentsAPI\Security\Models;

ini_set('display_errors', 1);
error_reporting(E_ALL);


use MatrixAgentsAPI\Shared\Models\MatrixGenericResponseModel;
use MatrixAgentsAPI\Security\Models\Interfaces\MatrixRegistrationResponseModelInterface;

class MatrixRegistrationResponseModel extends MatrixGenericResponseModel implements MatrixRegistrationResponseModelInterface
{

}