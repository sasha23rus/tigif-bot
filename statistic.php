<?
include_once('vendor/autoload.php');
include_once('assets/header.php');
use Lib\Main;

$s = Main::getFullStatistic();
?>

<div class="content text-center ">
	<h2 class="cover-heading">Статистика контента</h2>
	<main role="main" class="inner h-100 outer">

		<div class="lead inner">
			Всего: <?=$s[0]+$s[1]?><br>
			💛 GIF: <?=$s[0]?><br>
			💜 PIC: <?=$s[1]?><br>
			<br>
			<img src="/assets/diagramm.php?gif=<?=$s[0]?>&pic=<?=$s[1]?>" alt="">
		</div>
	
	</main>
</div>


<?include_once('assets/footer.php');?>
