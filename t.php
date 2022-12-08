<?
include_once('vendor/autoload.php');
include_once('assets/header.php');
use Lib\Main;

$s = Main::getFullStatistic();
?>

<?
    $type = (isset($_GET['t']))?$_GET['t']:'gif';
    $q = (isset($_GET['q']))?$_GET['q']:false;

?>


<div class="container text-center">
  <div class="row">
      <div class="col-md-6">
          <form action="t.php" >
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
          data-alt="(.+?)+?\.gif"

          <form action="t.php">
                <div class="mb-3">
                    <div class="form-check">
                          <input class="form-check-input" type="radio" name="t" value="gif" id="type1" <?=($type=='gif')?'checked':''?> >
                          <label class="form-check-label" for="type1">GIF</label>
                    </div>

                    <div class="form-check">
                          <input class="form-check-input" type="radio" name="t" value="pic" id="type2" <?=($type=='pic')?'checked':'';?>>
                          <label class="form-check-label" for="type2">PIC</label>
                    </div>
                    <label for="listFiles" class="form-label">Список файлов</label>
                    <textarea class="form-control" id="listFiles" name="addList" rows="3"></textarea>
                    <button class="btn btn-primary mb-3" type="submit" >Добавить</button>
                </div>
          </form>
      </div>

  </div>
</div>
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
                    <div class="alert alert-danger">
                        <? var_dump($img); ?>
                    </div>
                    <div class="alert alert-danger">
                        <?=$img['error']['message']?><br>
                    </div>
                    <?
                }else{
                    foreach ($img['items'] as $key => $value) {
                        $add = 1;
                        $value['query_search'] = $q;
                        $add = Main::addImage($type, $value['link'], $value);
                        if ($add) {
                            ?>
                            <div class="col-4">
                              <div class="p-3 border bg-light">
                                <img src="<?=$value['link']?>" alt="" width="100px">
                                <br>
                                    <?print_r($value['title'])?>
                              </div>
                            </div>
                            <?
                        }
                    }
                }
            }
            ?>

            <?if(isset($_GET['addList'])){
                  $addList = explode(';', $_GET['addList']);
                  foreach($addList as $pic){
                      $add = Main::addImage($type, $pic);
                      if ($add) {
                            ?>
                              <div class="col">
                                <img src="<?=$pic?>" alt="" width="100px">
                              </div>


                            <?
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

<?include_once('assets/footer.php');?>
