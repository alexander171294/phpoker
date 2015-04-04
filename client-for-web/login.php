<?php

session_start();

$_SESSION['server_data']['host'] = isset($_GET['host']) ? $_GET['host'] : '127.0.0.1';
$_SESSION['server_data']['port'] = isset($_GET['port']) ? $_GET['port'] : '6768';
$_SESSION['server_data']['id'] = $_GET['id'];

header('Location: client-version.html');