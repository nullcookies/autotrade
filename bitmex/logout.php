<?php
// dump(__FILE__);
if (!defined('IS_VALID')) define('IS_VALID', 1);
require_once ("main.php");

if (!session_id()) session_start();
// ============================================================ //

$_SESSION = null;
session_destroy();

echo 'Redirecting ...';
redirect('login.php', 1);
