<?php
require_once __DIR__ . '/../Utils/url.php';

if (!isset($_SESSION)) {
   session_start();
}

if (!isset($_SESSION['id'])) {
   header("Location: " . redirect_url('login'));
   die();
}

