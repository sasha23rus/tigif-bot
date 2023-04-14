<?
include_once('vendor/autoload.php');
include_once('assets/header.php');



use Lib\Main;

?>

<div class="text-center container-fluid">
	<div class="row">
		<div class="col">1</div>
		<div class="col">
			
			<h2 class="cover-heading">Голые сиськи</h2>
			<p class="lead" id="main_fraime">
				<img
					src="<?=Main::DBrandomImgae(htmlspecialchars($_GET['t'] ?? 'gif'))['URL']?>"
					class="img-fluid"
					style="max-height: 69vh"
					id="main_fraime_image">
			</p>

			<p class="lead">
			  <a href="?t=gif" class="btn btn-lg btn-secondary">gif</a>
			  <a href="?t=pic" class="btn btn-lg btn-secondary">pic</a>
			</p>
			
		</div>
		<div class="col">
			<?$picList = Main::getRandImages(9);?>
			<div class="media" style="">
				<?php foreach ($picList as $key => $value): ?>
					<img src="<?=$value['URL']?>" class="media-item" alt="Голые сиськи <?=$value['ID']?>" onClick="selectContent(this);" style="">
				<?php endforeach ?>
			</div>
		</div>
	</div>
</div>



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
