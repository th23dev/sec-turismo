<?php
require_once __DIR__ . '/../Utils/url.php';

if (!isset($_SESSION)) {
   session_start();
}
session_destroy();
header("Location: " . redirect_url('login'));
