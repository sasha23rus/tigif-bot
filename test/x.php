<?php
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
foreach (glob( $_SERVER["DOCUMENT_ROOT"]."/vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/main.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/myTG.php');

use Telegram\Bot\Api;
use Lib\Main;
use my\tg;

$type = 'pic';
$url = 'http://i.imgur.com/8GsnJBX.jpg';

$img = Main::getTopAll('GIF');

var_dump($img);
