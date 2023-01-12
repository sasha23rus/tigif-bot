<?php
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/assets/header.php');
use Lib\Main;

$full = $_REQUEST;

?>
<pre>
	<?print_r($full);?>
</pre>
