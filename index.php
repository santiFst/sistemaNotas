<?php
session_start();
if (!isset($_SESSION['docente'])) {
    header("Location: login/login.php");
    exit();
}
header("Location: dashboard.php");
exit();
