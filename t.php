<?
include_once('vendor/autoload.php');
include_once('assets/header.php');
use Lib\Main;

$s = Main::getFullStatistic();
?>
	
<?$type = ($_GET['t']=='')?'gif':$_GET['t'];?>

<a href="?i=<?=intval($_GET['i'])+1?>&t=<?=$type?>" class="btn btn-lg btn-secondary">next</a>
<br>
<div class="container text-center">
  <div class="row">
    <?
    
    // $img = Main::getImage(false, intval($_GET['i']));
    $img = Main::getImage($type, intval($_GET['i']));

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
            $add = Main::addImage($type, $value['link'], $value);
            if ($add) {
                ?>
                <div class="col">
                    <img src="<?=$value['link']?>" alt="" width="200px">
                    <br>
                        <?print_r($value['title'])?>
                </div>
                <?
            }
        }
    }
    ?>
  </div>
</div>
<br>
<a href="?i=<?=intval($_GET['i'])+1?>&t=<?=$type?>" class="btn btn-lg btn-secondary">next</a>




<?include_once('assets/footer.php');?>