<?
include_once('vendor/autoload.php');
include_once('assets/header.php');
use Lib\Main;

$s = Main::getFullStatistic();
?>
	

<main role="main" class="inner cover">
    
    <h1 class="cover-heading">Statistic</h1>

    <div class="lead">
    	Всего: <?=$s[2]?><br>
    	<i class="bi bi-square-fill" style="color: #FFC133;"></i> GIF: <?=$s[0]?><br>
    	<i class="bi bi-square-fill" style="color: #FF33D1;"></i> PIC: <?=$s[1]?><br>
    	<img src="/assets/diagramm.php?gif=<?=$s[0]?>&pic=<?=$s[1]?>" alt="">
    </div>

</main>


<?include_once('assets/footer.php');?>