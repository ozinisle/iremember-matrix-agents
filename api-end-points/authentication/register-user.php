<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include($_SERVER['DOCUMENT_ROOT'].'/products/iremember-matrix-agents/MatrixAgentsAPI/Shared/includeHeader.php');

use MatrixAgentsAPI\Security\Authenticator as MatrixAuth;

session_start();
session_regenerate_id();

$authenticator = new MatrixAuth();
echo $authenticator->register();
