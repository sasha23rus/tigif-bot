<?php
include_once('vendor/autoload.php');
foreach (glob( "vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once('lib/main.php');

//use Telegram\Bot\Api;
use Lib\Main;

$type = 'pic';
$url = 'http://i.imgur.com/8GsnJBX.jpg';

var_dump(Main::CheckReiting(2946, 363661763));
