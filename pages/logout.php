<?php
session_start();

session_unset();
session_destroy();

setcookie('user', $email, time() + (30 * 24 * 60 * 60), '/');

header('Location: login.php');
exit;
