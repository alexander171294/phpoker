<?php

session_start();

if(isset($_SESSION['server_data']))
{

echo 'var autoConnect = true; '.PHP_EOL;
echo 'var autoHost = \''.$_SESSION['server_data']['host'].'\'; '.PHP_EOL;
echo 'var autoPort = \''.$_SESSION['server_data']['port'].'\'; '.PHP_EOL;
echo 'var autoID = \''.$_SESSION['server_data']['id'].'\'; '.PHP_EOL;

} else {

echo 'alert(\'SERVER DATA FAILED\');';

}