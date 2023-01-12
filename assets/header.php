<?
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
foreach (glob( $_SERVER["DOCUMENT_ROOT"]."/vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}

include_once($_SERVER["DOCUMENT_ROOT"].'/lib/main.php');

use Lib\Site;
use Krugozor\Database\Mysql;

print(Site::header());
?>
