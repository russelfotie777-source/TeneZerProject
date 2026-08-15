<?php
session_start();
if (!isset($_SESSION['owner_logged_in']) || $_SESSION['owner_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
