<?php
Start the session


 Validate CSRF token
function validateCSRFToken($token) {
   if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
     http_response_code(403);
     exit('Invalid CSRF token');
  }
 }

// Check if CSRF token is already set
 if (!isset($_SESSION['csrf_token'])) {
   Generate a new CSRF token
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
 }

 $csrf_token = $_SESSION['csrf_token'];
?> 
