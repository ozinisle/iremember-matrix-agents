<?php
include('../../MatrixAgentsAPI/shared/includeHeader.php');

use MatrixAgentsAPI\Security\Authenticator as MatrixAuth;

// session.use_strict_mode= 1;
// session.cookie_secure = 1;
// session.use_only_cookies = 1;
// session.cookie_httponly = 1;
// session.hash_function = 1;
// session.hash_bits_per_character=6;

session_start();
session_regenerate_id();

$authenticator = new MatrixAuth();
echo $authenticator->login();
