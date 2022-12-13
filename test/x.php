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

$img = Main::getImageById(1);

$result["message"]["chat"]["id"] = 153057273;
$result["message"]["from"]['id'] = 5924175794;
$telegram = new Api('5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE', true);

tg::localSend($img, $result, $telegram);
