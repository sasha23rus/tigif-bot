<?
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
foreach (glob( $_SERVER["DOCUMENT_ROOT"]."/vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/myTG.php');
use my\tg;
/*$params = [
    'chat_id' => 153057273,
    'animation' => 'https://c.radikal.ru/c29/2009/1c/484741a15302.gif',
];
$out = tg::sendTG('sendAnimation', $params);
var_dump($out->result->animation->file_id);
echo "<pre>";
    print_r($out);
echo "</pre>";*/



