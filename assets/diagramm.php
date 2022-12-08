<?header('Content-type: image/gif');
include_once('../lib/dia.php');
use Diagram\dia;
$instance = new dia();
$data=array('FFC133'=>intval($_GET['gif']), 'FF33D1'=>intval($_GET['pic']), '8B0000' => intval($_GET['mov']) );
$instance->generate($data);
?>
