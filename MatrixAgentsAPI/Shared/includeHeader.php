<?php
//NOTE - change the header to production url in production
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json");

include('../../vendor/autoload.php');

//include($_SERVER['DOCUMENT_ROOT'].'/products/iremember-matrix-agents/vendor/autoload.php');

//ini_set('display_errors', 1);
//error_reporting(E_ALL);
