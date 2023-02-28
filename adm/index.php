<?
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/assets/header.php');
use Lib\Main;

//$s = Main::getFullStatistic();
?>

<?
    $type = (isset($_GET['t']))?$_GET['t']:'gif';
    $q = (isset($_GET['q']))?$_GET['q']:false;

?>


<div class="container text-center">
  <div class="row">
      <div class="col-md-6">
		  
          <form >
              <input type="hidden" name="i" value="0">
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="t" value="gif" id="type1" <?=($type=='gif')?'checked':''?> >
                  <label class="form-check-label" for="type1">GIF</label>
              </div>

              <div class="form-check">
                  <input class="form-check-input" type="radio" name="t" value="pic" id="type2" <?=($type=='pic')?'checked':'';?>>
                  <label class="form-check-label" for="type2">PIC</label>
              </div>

              <div class="input-group mb-3">
                  <input type="text" class="form-control" placeholder="Что ищем?" aria-label="Что ищем?" aria-describedby="search" name="q" value="<?=$q?>">
                  <button class="btn btn-outline-secondary" type="submit" id="search">Искать</button>
              </div>

          </form>
		  <a href="https://www.vampire69blog.com/">Vampire 69 Porn</a>
      </div>
      <div class="col-md-6">
          Разделитель ;

          <form action="index.php" method="post">
                <div class="mb-3">
                    <label for="listFiles" class="form-label">Список файлов</label>
                    <textarea class="form-control" id="listFiles" name="addList" rows="3"></textarea>
                    <button class="btn btn-primary mb-3" type="submit" >Добавить</button>
                </div>
          </form>
		  Регулярно добавляются, можно брать
		  <ul>
			  <li><a href="https://whatboyswant.com/forums/babes/famous-babes/flash-drop-reveal-boobs-gifs--168406?page=15" target="_blank">https://whatboyswant.com/forums/babes/famous-babes/flash-drop-reveal-boobs-gifs--168406?page=15</a></li>
		  </ul>
      </div>

  </div>
</div>

<!--результаты поиска-->
<?nextBTN($type, $q)?>
<br>
<div class="container overflow-hidden text-center">
	<div class="row gy-5">
		<?
		// $img = Main::getImage(false, intval($_GET['i']));
		if ($q){
			$img = Main::getImage((string) $q, $type, intval($_GET['i']));
			if (isset($img['error'])) {
				?>
				<h2>Ошибка</h2>
				<div class="alert alert-danger">
					<? var_dump($img); ?>
				</div>
				<div class="alert alert-danger">
					<?=$img['error']['message']?><br>
				</div>
				<?
			}else{
				?>
				<h2>Результаты <?=count($img['items'])?:0?></h2>
					<button class="btn btn-primary -add-all-" id="add_all">Добавить все</button>
					<div id="result_list" class="row gy-3">
						<?
						foreach ($img['items'] as $key => $value) {
							?>
							<div class="col-3 -add-pic-" data-key="<?=$key?>" >
								<form class="form-dialog-content-<?=$key?>">
								<div class="p-3 border bg-light">
									<img src="<?=$value['link']?>" alt="" width="270px" class="-add-" style="cursor: pointer">
								</div>
								<input type="hidden" name="array_<?=$key?>" value='<?=json_encode($value)?>'>
								<input type="radio" name="type" value="gif" <?=($type=='gif')?'checked':''?>>gif
								<input type="radio" name="type" value="pic" <?=($type=='pic')?'checked':''?>>pic
								<input type="hidden" name="query_search" value="<?=$q?>">
								<div class="answer">&nbsp;</div>
								</form>
							</div>
							<?
						}
						?>
					</div>
				<?
			}
		}
		?>
		
		<?if(isset($_POST['addList'])){
		  $addList = explode(';', $_POST['addList']);
		  
		  foreach($addList as $pic){
			  $pic = trim($pic);
			  $validUrl = filter_var($pic, FILTER_VALIDATE_URL);
			  if ($validUrl){
				  $fileInfo = new SplFileInfo($validUrl);
				  if ($fileInfo->getExtension() == 'gif'){ $type = 'gif'; }
				  elseif (
						$fileInfo->getExtension() == 'jpg'  ||
						$fileInfo->getExtension() == 'jpeg' ||
						$fileInfo->getExtension() == 'png'  ||
						$fileInfo->getExtension() == 'webp'
				  ){ $type = 'pic'; }
				  elseif ($fileInfo->getExtension() == 'mp4'){ $type = 'mov';}
				  else{
						$stop = true;
				  }
	
				  if (!$stop){
					  $x = Main::addImage($type, $pic, $value, 'Y');
					  //$add = Main::addImage($type, $pic);
					  if (intval($x)>0) {
						  ?>
							  <div class="col">
								<img src="<?=$pic?>" alt="" width="100px"><br>/sendpic_<?=$x?>
							  </div>
						  <?
					  }
				  }
	
			  }
		  }
	}?>
	</div>
</div>
<br>


<script>
(() => {
	document.addEventListener('click', async (event) => {

		if (event.target.closest('.-add-pic-')) {
	
			if (!event.target.closest('.-add-')) return;
		
			const block = event.target.closest('.-add-pic-');
			add_content(block);
		}
		
		if (event.target.closest('.-add-all-')){
			if (!event.target.closest('.-add-all-')) return;
			let childDivs = document.getElementById('result_list').getElementsByClassName('-add-pic-');

			for( i=0; i< childDivs.length; i++ ) {
				let block = childDivs[i];
				add_content(block);
			}
		}
	});
	
	function add_content(block){
		const key = block.getAttribute('data-key');
		const answer = block.querySelector(".answer");
		const form = document.querySelector(".form-dialog-content-"+key);
		let url = "/adm/add_post.php";
		answer.textContent = 'sending...';
	
		const formData  = new FormData(form)
			formData.append('key', key);
		fetch(url,{method:'POST', body: formData})
		.then((response)=>response.json())
		.then((result)=>{
			console.log(result);
			if(result.status === true){
				answer.textContent = 'ok';
			}else{
				answer.textContent = 'err';
				// document.getElementById("err_form").innerHTML = result.error;
			}
		})
	}
})();


</script>

<?nextBTN($type, $q)?>

<?php
function nextBTN($type, $q = ''){
    if ($q){
        ?>
        <a href="?i=<?=intval($_GET['i'])+1?>&t=<?=$type?>&q=<?=urlencode($q) ?>" class="btn btn-lg btn-secondary">next</a>
        <?
    }
}
?>

<?include_once($_SERVER["DOCUMENT_ROOT"].'/assets/footer.php');?>
