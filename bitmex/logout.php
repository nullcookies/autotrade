<?php
// dump(__FILE__);
defined('IS_VALID') or define('IS_VALID', 1);
require_once ("main.php");

if (!session_id()) session_start();

// ------------------------------------------------------------ //

$_SESSION = null;
session_destroy();

echo 'Redirecting ...';
func_redirect('login.php', 1);
