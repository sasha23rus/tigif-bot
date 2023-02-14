<?
namespace Lib;

use Telegram\Bot\Api;
use Krugozor\Database\Mysql;
use Krugozor\Database\Statement;
use Krugozor\Database\MySqlException;
use Diagram\dia;


class Main{

	public static function DB(){
		return Mysql::create("localhost", "sasha23_tibot", "ssU6p9jM")
		->setErrorMessagesLang('ru')
		->setDatabaseName("sasha23_tibot")
		->setCharset("utf8")
		->setStoreQueries(false);
	}

	/**
	 *
	 * GOOGLE
	 *
	 */
	public static function getImage($query, $type, $start = 0){
		$imgType = ($type=='pic')?'photo':'animated';
		$queryParams = [
		    'cx' 	=> '278b988d6545e4b27',
		    'imgType' => $imgType,
		    'q'		=> $query,
		    'safe'	=> false,
		    'searchType'=> 'image',
		    'sort'	=> 'date:d',
		    'key' 	=> 'AIzaSyDFNyTrlssWgzHFXyTVc7vArsa9lj3DC4o',
		    'excludeTerms'=>'мем, акция, купить, песня, домашнее, член, смешные, ебется, joyreactor, вибратор, пожелание, SEX.COM, Шмель, shemale',
		    'start'	=> ($start>0)?$start*10:0
		];
		$url = 'https://customsearch.googleapis.com/customsearch/v1?'.http_build_query($queryParams);
		$headers = ['Content-Type: application/json']; // создаем заголовки

		$curl = curl_init(); // создаем экземпляр curl

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_POST, false); //
		curl_setopt($curl, CURLOPT_URL, $url);

		$result = curl_exec($curl);

		$json = json_decode($result, true);
        echo "<hr>";
        var_dump($query);
        echo "<hr>";
		return $json;
	}

	public static function getSingleImage($type='', $repeat=0){
		return self::DBrandomImgae($type, $repeat);
	}

    //тест
    public static function getSingleImageNEW($type='GIF', $repeat = 0){
		if ($repeat){
			return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` ="?s" AND `FILE_ID` != "" ORDER BY RAND() LIMIT 1 ', strtoupper($type))->fetchAssoc();
		}else{
        	return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` ="?s" AND `FILE_ID` = "" ORDER BY RAND() LIMIT 1 ', strtoupper($type))->fetchAssoc();
		}
    }

	public static function DBrandomImgae($type='gif', $repeat = 0){
		if ($repeat){
			return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` ="?s" AND `FILE_ID` != "" ORDER BY RAND() LIMIT 1', strtoupper($type))->fetchAssoc();
		}else{
			return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` ="?s" ORDER BY RAND() LIMIT 1', strtoupper($type))->fetchAssoc();
		}
	}
	public static function DBrandomContent($repeat = 0){
		if ($repeat){
			return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `FILE_ID` != "" ORDER BY RAND() LIMIT 1')->fetchAssoc();
		}else{
			return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 ORDER BY RAND() LIMIT 1')->fetchAssoc();
			
		}
	}



	public static function getFullStatistic(){
		$gif = Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` = "GIF"');
    	$gif_row = intval($gif->getNumRows());
    	$pic = Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` = "PIC"');
    	$pic_row = intval($pic->getNumRows());
        $mov = Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ACTIVE` = 1 AND `TYPE` = "MOV"');
    	$mov_row = intval($mov->getNumRows());

    	return array($gif_row, $pic_row, $mov_row, ($gif_row + $pic_row+$mov_row));
	}

	public static function setReitingBtn($pic_id, $action){
		$arData = array(
			'method'=>'reitng',
			'id'=> $pic_id,
			'action'=>$action
		);
		return implode("|",$arData);
	}
    public static function AdminBtns($pic_id, $new = ''){
		$keyboard[] = array(
            'text'=>'⬅️Prev',
            'callback_data'=> implode("|",array('method'=>'previous', 'id'=> $pic_id, 'action'=>'getprev'))
        );
		if($new){
			$keyboard[] = array(
				'text'=>'🆗',
				'callback_data'=> self::setReitingBtn($pic_id, 'confirmnow')
			);
		}
        $keyboard[] = array(
            'text'=>'⛔',
            'callback_data'=> self::setReitingBtn($pic_id, 'removenow')
        );
		$keyboard[] = array(
            'text'=>'Next ➡',
            'callback_data'=> implode("|",array('method'=>'next', 'id'=> $pic_id, 'action'=>'getnext'))
        );
        return $keyboard;
    }
	public static function reitingBtns($pic_id){
		$reitLIKE = self::getReiting($pic_id, 'like');
		$reitDIS = self::getReiting($pic_id, 'dislike');
		$reitBAN = self::getReiting($pic_id, 'ban');

		$keyboard = array(
	        array(

	          array(
	          	'text'=>($reitDIS>0)?'👎 '.$reitDIS:'👎',
	          	'callback_data'=> self::setReitingBtn($pic_id, 'dislike')
	          ),
	          array(
	          	'text'=>'#'.$pic_id,
	          	'callback_data'=> self::setReitingBtn($pic_id, 'info')
	          ),
	          array(
	          	'text'=>($reitBAN>0)?' 🚫'.$reitBAN:'🚫',
	          	'callback_data'=> self::setReitingBtn($pic_id, 'ban')
	          ),

	          array(
	          	'text'=>($reitLIKE>0)?' 👍'.$reitLIKE:'👍',
	          	'callback_data'=> self::setReitingBtn($pic_id, 'like')
	          ),

	        )
	    );
        /*if ($from == 153057273){
            $keyboard[1] = self::AdminBtns($pic_id, $from);
        }*/

	    return $keyboard;
	}

	public static function getImageById($id){
	    return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `ID` = "?s" LIMIT 1', $id)->fetchAssoc();
	}

	public static function setImageFileID($id, $fid){
	    Main::DB()->query('UPDATE `GIF_TABLE` SET FILE_ID = "?s" WHERE ID = "?i"', $fid, $id);
	}

    public static function CheckReiting($id, $from){
        $db = Main::DB();
        return $db->query('SELECT `ID` FROM `RAITING` WHERE `ID_PIC` = "?i" AND `USER_ID` = "?i" LIMIT 1', $id, $from)->fetchRow()[0];
    }
	public static function setReiting($id, $from, $action, $file)
	{
		$db = Main::DB();

		$data = array(
			'ID_PIC' => $id,
			'USER_ID' => $from,
			'ACTION' => $action
		);
        if ($action!='info' && $action!='removenow' && $action!='confirmnow') $db->query('INSERT INTO `RAITING` SET ?As', $data);


    	$img = self::getImageById($id);
    	$count = intval($img['RAITING']);
    	if (!$img['FILE_ID']) {
			self::setImageFileID($id, $file);
		}

    	if ($action=='ban') {
    		$count--;
    		$db->query('UPDATE `GIF_TABLE` SET RAITING = "?i" WHERE ID = "?i"', $count, $id);
    		if(self::getReiting($id, $action) >= 4){
	    		$db->query('UPDATE `GIF_TABLE` SET ACTIVE = "0" WHERE ID = "?i"', $id);
    		}
    	}

    	if ($action=='dislike') {
    		$count--;
    		$db->query('UPDATE `GIF_TABLE` SET RAITING = "?i" WHERE ID = "?i"', $count, $id);
    	}

    	if ($action=='like') {
    		$count++;
    		$db->query('UPDATE `GIF_TABLE` SET RAITING = "?i" WHERE ID = "?i"', $count, $id);
    	}


    	return $db->getAffectedRows();
	}
    public static function setDeactive($id){
        Main::DB()->query('UPDATE `GIF_TABLE` SET ACTIVE = "0", RAITING = "-1000", USER_CONFIRM = "1" WHERE ID = "?i"', $id);
    }
	public static function setDeactiveNotOpen($id){
        Main::DB()->query('UPDATE `GIF_TABLE` SET ACTIVE = "0", INFO = "NO_OPEN", USER_CONFIRM = "1" WHERE ID = "?i"', $id);
    }
    public static function setActive($id){
        Main::DB()->query('UPDATE `GIF_TABLE` SET ACTIVE = "1", USER_CONFIRM = "1" WHERE ID = "?i"', $id);
    }

	public static function getReiting($id, $action){
	    $result = Main::DB()->query('SELECT * FROM `RAITING` where `ID_PIC` = "?i" AND `ACTION` = "?s"', $id, $action);
	    $res = intval($result->getNumRows());

	    return $res;
	}

	public static function checkIMGinBase($url){
	    return Main::DB()->query('SELECT * FROM `GIF_TABLE` where `URL` = "?s" LIMIT 1', $url)->getNumRows();
	}

	public static function getRandImages($count = '5')
	{
	    $result = Main::DB()->query('SELECT * FROM `GIF_TABLE` where `TYPE` = "PIC" AND `ACTIVE` = 1 ORDER BY RAND() LIMIT '.$count );

	    while($data = $result->fetchAssoc()){
	    	$res[] = $data;
	    }

	    return $res;
	}
	public static function getRandGIFs($count = '5')
	{
	    $result = Main::DB()->query('SELECT * FROM `GIF_TABLE` where `TYPE` = "GIF" AND `ACTIVE` = 1 ORDER BY RAND() LIMIT '.$count );

	    while($data = $result->fetchAssoc()){
	    	$res[] = $data;
	    }

	    return $res;
	}

	public static function setViewCount($id)
	{
		Main::DB()->query('UPDATE `GIF_TABLE` SET `COUNT` = COUNT + 1 WHERE `ID` = "?i"', $id);
	}


	/**
	 *
	 * Работа с пользователем
	 *
	 */
	public static function User($array)
	{
		if(!self::SearchUser($array['id'])){
			self::AddUser($array);
		}
		return true;
	}
	public static function SearchUser($id)
	{
		return Main::DB()->query('SELECT * FROM `USERS` WHERE `USER_ID` = "?i"', $id)->fetchAssoc() ?? false;
	}
	public static function AddUser($ar)
	{
		$data = array(
			'NIC' 		=> $ar['username'],
			'USER_ID' 	=> $ar['id'],
			'FIRST_NAME'=> $ar['first_name'],
			'LAST_NAME' => $ar['last_name']
		);

    	Main::DB()->query('INSERT INTO `USERS` SET ?As', $data);
    	return;
	}
	public static function FindAllUsers()
	{
		return Main::DB()->query('SELECT `ID` FROM `USERS`')->getNumRows();
	}

	/**
	 *
	 * Работа с сообщениями /добавляем контент
	 *
	 */

	/*public static function addImage($type, $url, $info = ''){
	if (!self::checkIMGinBase($url) > 0) {
		$data = array(
			'ACTIVE' => 1,
			'URL' => $url,
			'TYPE' => strtoupper($type)
		);
		if ($info)
			$data['INFO'] = serialize($info);

		$res = Main::DB();
		$res->query('INSERT INTO `GIF_TABLE` SET ?As', $data);
		return $res->getLastInsertId();
	}*/
	public static function addImage($type, $url, $info = '', $adm = ''){
		if (!self::checkIMGinBase($url) > 0) {
			$data = array(
				'ACTIVE' => 1,
				'URL' => $url,
				'TYPE' => strtoupper($type),
				'USER_CONFIRM' => 1
			);
			if (!$adm){
				$data['ACTIVE'] = 0;
				$data['USER_CONFIRM'] = 0;
			}
			if ($info)
				$data['INFO'] = serialize($info);

            $res = Main::DB();
            $res->query('INSERT INTO `GIF_TABLE` SET ?As', $data);
	    	return $res->getLastInsertId();
		}else{
			return 'double';
		}
	}
	public static function addContent($type, $sendresult, $uid){
		if ($uid == '153057273') {
			$admin = 'Y';
		}
		$res = Main::DB();
		$dataFile = array(
			'file_id' => $sendresult['file_id'],
			'file_unique_id'=>$sendresult['file_unique_id'],
			'file_size'=>$sendresult['file_size'],
			'file_path'=>$sendresult['file_path'],
			'user_name'=>$sendresult['user_name'],
			'user_uid'=>$sendresult['user_uid'],
			'type'=>$sendresult['type'],
			'thumb'=>$sendresult['thumb'],
			'file_name'=>$sendresult['file_name'],
			'mime_type'=>$sendresult['mime_type'],
		);
		$res->query('INSERT INTO `FILES` SET ?As', $dataFile);
		$id = $res->getLastInsertId();
		$data = array(
			'ACTIVE' => 1,
			'URL' => $sendresult["info"]["link"],
			'INFO' => serialize($sendresult["info"]),
			'TYPE' => strtoupper($type),
			'FILE_ID'=> $sendresult['file_id'],
			'FILE'=> $id,
			'USER_CONFIRM'=> 1
		);
		if ($admin != 'Y'){
			$data['ACTIVE'] = 0;
			$data['USER_CONFIRM'] = 0;
		}
		$res->query('INSERT INTO `GIF_TABLE` SET ?As', $data);
		return $res->getLastInsertId();
	}

	 /**
	  *
	  * Экспериментальный блок
	  *
	  */

	public static function addGameText($text)
	{
		Main::DB()->query('INSERT INTO `Game` SET Text = "?s" ', $text);
	}

	/**
	 *
	 * Игра
	 *
	 */

	public static function getGameText()
	{
		return  Main::DB()->query('SELECT TEXT FROM `Game` ORDER BY RAND() LIMIT 1 ')->fetchRow()[0];
	}

    /**
	 *
	 * TOP
	 *
	 */

    public static function setTopBtn($action): string
    {
		$arData = array(
            'method'=>'top',
            'action'=>$action
        );
		return implode("|",$arData);
	}
    public static function TopBtns(): array
    {
        $keyboard = array(
	        array(
	            array(
	          	    'text'=>'TOP GIF`s all time',
	          	    'callback_data'=> self::setTopBtn('gif_all')
	            ),
	            array(
                    'text'=>'TOP GIf`s week',
                    'callback_data'=> self::setTopBtn('gif_7')
                ),
	        ),
            array(
                array(
	          	    'text'=>'TOP PIC`s all time',
	          	    'callback_data'=> self::setTopBtn('pic_all')
	            ),
	            array(
                    'text'=>'TOP PIC`s week',
                    'callback_data'=> self::setTopBtn('pic_7')
                )
            )
	    );

	    return $keyboard;
    }
    public static function getTopAll($type){
        $result =  Main::DB()->query('SELECT * FROM `GIF_TABLE` WHERE `ACTIVE` = 1 AND `TYPE` = "?s" ORDER BY `RAITING` DESC  LIMIT 3', $type);
        while($data = $result->fetchAssoc()){
	    	$res[] = $data;
	    }
	    return $res;
    }
	public static function getTopWeek($type)
	{
		$result = Main::DB()->query('SELECT * FROM `GIF_TABLE` WHERE `ACTIVE` = 1 AND `TYPE` = "?s" AND `DATE` > DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY `RAITING` desc  LIMIT 3', $type);
		while ($data = $result->fetchAssoc()) {
			$res[] = $data;
		}
		return $res;
	}
	
	public static function getNextContent($pic_id){
		$res = Main::DB();
		$nowContetnType = $res->query('SELECT `TYPE` FROM `GIF_TABLE` WHERE `ID` = "?i" LIMIT 1', $pic_id)->fetchAssoc()['TYPE'];
		
		$nextCotnten = $res->query('SELECT * FROM `GIF_TABLE` WHERE `ID` > "?i" AND `ACTIVE` = 1 AND `TYPE` = "'.$nowContetnType.'" LIMIT 1', $pic_id)->fetchAssoc();
		
		return $nextCotnten;
	}
	public static function getPreContent($pic_id){
		$res = Main::DB();
		$nowContetnType = $res->query('SELECT `TYPE` FROM `GIF_TABLE` WHERE `ID` = "?i" LIMIT 1', $pic_id)->fetchAssoc()['TYPE'];
		
		$nextCotnten = $res->query('SELECT * FROM `GIF_TABLE` WHERE `ID` < "?i" AND `ACTIVE` = 1 AND `TYPE` = "'.$nowContetnType.'" ORDER BY `ID` DESC LIMIT 1', $pic_id)->fetchAssoc();
		
		return $nextCotnten;
	}
	
	public static function IsAdmin($uid){
		if( $uid == '153057273' ){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getNewContent(){
		$res = Main::DB();
		$newCotntent = $res->query('SELECT * FROM `GIF_TABLE` WHERE `USER_CONFIRM` = 0 AND `ACTIVE` = 0 ORDER BY `ID` ASC LIMIT 1')->fetchAssoc();
		return $newCotntent;
	}

}


class Site
{
	public static function header(){
		?>
		<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<title>Ti Bot</title>
				<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
				<link rel="stylesheet" href="https://atuin.ru/demo/mosaic/jquery.mosaic.min.css">


				<link href="/assets/css/cover.css" rel="stylesheet">

				<script
  src="https://code.jquery.com/jquery-3.6.1.min.js"
  integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
  crossorigin="anonymous"></script
			</head>
			<body class="text-center">
				<div class="container text-center">
				  <div class="row align-items-start">
				    <div class="col">
				      <h3 class=""><a href="/">TiBot @tigif_bot</a></h3>
				    </div>
				    <div class="col">
				     	<nav class="nav nav-masthead justify-content-center">

					        <a class="nav-link<?=($_SERVER['SCRIPT_NAME']=='/index.php')?' active':'';?>" href="/">Home</a>
					        <a class="nav-link<?=($_SERVER['SCRIPT_NAME']=='/statistic.php')?' active':'';?>" href="/statistic.php">Statistic</a>
					        <a class="nav-link" href="#">#</a>

					      </nav>
				    </div>
				  </div>
				</div>

				<?php if (!$_SERVER['SCRIPT_NAME']=='/index.php'): ?>
					<div class="container text-center">
						<div class="row align-items-center">
					    <div class="col"></div>
					    <div class="col">
							<div class="container d-flex h-100 p-3 mx-auto flex-column">

				<?php endif ?>

		<?
	}

	public static function footer(){
		?>
				<?php if (!$_SERVER['SCRIPT_NAME']=='/index.php'): ?>
						    </div>
				    	</div>
					    <div class="col"></div>
					  </div>
					</div>

				<?php endif ?>

				<footer class="mastfoot mt-auto">
			        <div class="inner">
			          <p>Telegram <a href="https://t.me/tigif_bot">@tigif_bot</a>, by <a href="3">me</a>.</p>
			        </div>
			    </footer>

				<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

				<script src="https://atuin.ru/demo/mosaic/jquery.mosaic.min.js" ></script>
			</body>
		</html>
		<?
	}
}
