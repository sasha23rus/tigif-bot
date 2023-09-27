<?
include_once('vendor/autoload.php');
include_once('assets/header.php');



use Lib\Main;

?>
<div class="container text-center">
	<h2>Голые сиськи</h2>
</div>
<section class="text-center container-fluid">
	<div class="row align-items-center h-100">
		<div class="col h-100 d-none d-md-block text-center ">
			<?$gifList = Main::getRandGIFs(9);?>
			<div class="media align-self-middle">
				<?php foreach ($gifList as $key => $value): ?>
					<?
					$img = validImg($value['URL']);
					?>
					<img src="<?=$img['URL']?>" class="media-item" onClick="selectContent(this);" style="">
				<?php endforeach ?>
			</div>
		</div>
		
<!--		<div class="col border text-center h-100"  >-->
		<div class="col outer" style="" >
			<div class="text-center inner" id="main_fraime">
				<?$img = Main::DBrandomImgae(htmlspecialchars($_GET['t'] ?? 'gif'));?>
				<img
					src="<?=validImg($img['URL'], $_GET['t'] ?? 'gif')['URL']?>"
					class="img-fluid"
					style="max-height: 69vh"
					id="main_fraime_image">
				<p id="description border">
					#Команда в боте <a href="https://t.me/tigif_bot" target="_blank"><b>/sendpic_<?=$img['ID']?></b></a>
				</p>
	
				<p class="lead flex">
				  <a href="?t=gif" class="btn btn-lg btn-secondary flex-left">gif</a>
				  <a href="?t=pic" class="btn btn-lg btn-secondary flex-right">pic</a>
				</p>
			</div>
			
		</div>
		
		<div class="col h-100 d-none d-md-block text-center">
			<?$picList = Main::getRandImages(9);?>
			<div class="media align-self-middle">
				<?php foreach ($picList as $key => $value): ?>
					<?
					$img = validImg($value['URL'], 'pic');
					?>
					<img src="<?=$img['URL']?>" class="media-item" onClick="selectContent(this);" style="">
				<?php endforeach ?>
			</div>
		</div>
	</div>
</section>

<?php
	function validImg($img, $type = 'gif'){
		if (!file_exists($img) || filesize($img) < 128) {
		   $img = Main::DBrandomImgae($type);
		}
		return $img;
	}
?>

<!--<div class="container text-center content full_vh ">
	<div class="row align-items-center">
		<div class="col d-none d-sm-block">
			<h6 class="cover-heading">Some GIF`s for You!</h6>

			<?/*$picList = Main::getRandGIFs(9);*/?>
			<div id="myMosaic0" class="img_block">
				<?php /*foreach ($picList as $key => $value): */?>
					
			    <img border="0" src="<?/*=$value['URL']*/?>" onClick="selectContent(this);" />
				<?php /*endforeach */?>
			</div>
		</div>

		<div class="col offset-2">
			<div class="align-middle">
			    <h1 class="cover-heading">Its for You!</h1>
			    
			    <p class="lead" style="min-width: 600px;" if="main_fraime">
				    <img src="<?/*=Main::DBrandomImgae(htmlspecialchars($_GET['t'] ?? 'gif'))['URL']*/?>"  class="img-fluid" id="main_fraime_image">
					</p>

				    <p class="lead">
				      <a href="?t=gif" class="btn btn-lg btn-secondary">gif</a>
				      <a href="?t=pic" class="btn btn-lg btn-secondary">pic</a>
				    </p>
			</div>
  	</div>
    <div class="col d-none d-sm-block">
    
			<h6 class="cover-heading">Some Pic`s for You!</h6>

			<?/*$picList = Main::getRandImages(9);*/?>
			<div id="myMosaic1" class="img_block">
				<?php /*foreach ($picList as $key => $value): */?>
			    <img border="0" src="<?/*=$value['URL']*/?>" onClick="selectContent(this);" />
				<?php /*endforeach */?>
			</div>
  		
    </div>
  </div>
</div>-->



<script>
   /* $(function() {
    		$('#myMosaic0').Mosaic({
            maxRowHeight: 100,
            maxRowHeightPolicy: 'oversize',
            innerGap: 5
        });

        $('#myMosaic1').Mosaic({
            maxRowHeight: 100,
            maxRowHeightPolicy: 'oversize',
            innerGap: 5
        });
    });*/
</script>

<script>
	$(function() {
	});
		function selectContent(e){
			let src = $(e).attr('src');
			$('#main_fraime_image').attr('src', src);
		}
</script>

<?include_once('assets/footer.php');?>
