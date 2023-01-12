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
      </div>
      <div class="col-md-6">
          https://nicetits.ru/gif/shikarnye-bolshie-siski?page=4
          <br>
          https://nicetits.ru/adult-videos?page=6
          <br>
          data-alt="(.+?)+?\.gif"

          <form action="t.php">
                <div class="mb-3">
                    <label for="listFiles" class="form-label">Список файлов</label>
                    <textarea class="form-control" id="listFiles" name="addList" rows="3"></textarea>
                    <button class="btn btn-primary mb-3" type="submit" >Добавить</button>
                </div>
          </form>
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
				<form action="/adm/add_post.php" method="post">
					<?
					foreach ($img['items'] as $key => $value) {
						$add = 1;
						$value['query_search'] = $q;
						//$add = Main::addImage($type, $value['link'], $value);
						//if ($add) {
							?>
							<div class="col-4">
								<div class="p-3 border bg-light">
									<input type="checkbox" name="key[]" value="<?=$key?>">
									<img src="<?=$value['link']?>" alt="" width="100px">
									<!--<br>
									<pre>
										<?/*print_r($value)*/?>
									</pre>-->
								</div>
							</div>
							<input type="hidden" name="array_<?=$key?>" value='<?=json_encode($value)?>'>
							<?
						//}
					}
					?>
					<input type="submit" name="save">
				</form>
				<?
			}
		}
		?>
		
		<?if(isset($_GET['addList'])){
		  $addList = explode(';', $_GET['addList']);
		  foreach($addList as $pic){
			  $validUrl = filter_var($pic, FILTER_VALIDATE_URL);
			  if ($validUrl){
				  $fileInfo = new SplFileInfo($validUrl);
				  if ($fileInfo->getExtension() == 'giv'){ $type = 'gif'; }
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
					  $add = Main::addImage($type, $pic);
					  if ($add>0) {
						  ?>
							  <div class="col">
								<img src="<?=$pic?>" alt="" width="100px">
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
