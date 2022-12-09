<?
include_once('vendor/autoload.php');
foreach (glob( "vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}

include_once('lib/main.php');

use Lib\Site;
use Krugozor\Database\Mysql;

print(Site::header());
?>
