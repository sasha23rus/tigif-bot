<?
include_once('vendor/autoload.php');
include_once('assets/header.php');
use Lib\Main;


$user = 2147483647;
$result = Main::SearchUser($user);
var_dump($result['ID']);
?>



<?include_once('assets/footer.php');?>
