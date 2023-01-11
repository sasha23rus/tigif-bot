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

$id = intval($_REQUEST['FILE']);
if ($id>0){
    $arContent = Main::getImageById($id);
    if ($arContent['FILE_ID']){
        $send = tg::sendTG('getFile', ['file_id' => $arContent['FILE_ID'] ]);
        $image_url = 'https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$send->result->file_path;
        var_dump($image_url);

        /*$image_info = getimagesize($image_url);
        header('Content-type: ' . $image_info['mime']);
        readfile($image_url);*/
    }
}
