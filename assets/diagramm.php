<?header('Content-type: image/gif');
include_once('../lib/dia.php');
use Diagram\dia;
$instance = new dia();
$data=array('FF33D1'=>intval($_GET['pic']), 'FFC133'=>intval($_GET['gif']));
$instance->generate($data);
?>