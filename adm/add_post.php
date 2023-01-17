<?php
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
foreach (glob( $_SERVER["DOCUMENT_ROOT"]."/vendor/krugozor/database/src/*.php") as $filename) { require_once $filename; }
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/main.php');

use Lib\Main;

$out = file_get_contents('php://input');
$key = $_REQUEST['key'];
$type = $_REQUEST['type'];
$value =  (array) json_decode($_REQUEST['array_'.$key]);
$value['query_search'] = $_REQUEST['query_search'];
$x = Main::addImage($type, $value['link'], $value);
if (intval($x)>0){
	$return = array('status'=>true, 'out'=>$value);
}else{
	$return = array('status'=>false, "out"=>$value);
}
echo json_encode($return);
?>
