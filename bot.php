<?
include_once('vendor/autoload.php');
foreach (glob( "vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once('lib/main.php');

use Telegram\Bot\Api;
use Telegram\Bot\Tests;
use Lib\Main;

$telegram = new Api('5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE', true);

$result = $telegram->getWebhookUpdates();
$text = $result["message"]["text"];
$chat_id = $result["message"]["chat"]["id"];
$name = $result["message"]["from"]["username"];



if($result["callback_query"]){
	$callback_data = explode('|',$result["callback_query"]['data']);
	if ($callback_data[0]=="reitng") {
		$id = $callback_data[1];
		$from = $callback_data[2];
		$action = $callback_data[3];
		$file = $result["callback_query"]['message']['photo'];
		if (!is_array($file)) {
			$file_id = $result["callback_query"]['message']['animation']['file_id'];
		}else{
			$file_id = $file[0]['file_id'];
		}
		Main::setReiting($id, $from, $action, $file_id);


		if ($action=='info') {
			$img = Main::getImageById($id);
			$arInfo = unserialize($img['INFO']);

            $info = "Ссылка на этот контент /sendpic ".$id;
            if ($arInfo['title'])   $info .= "\n".$arInfo['title'];
            if ($arInfo['link'])    $info .= "\nСсылка на оригинал ".$arInfo['link'];
            if ($arInfo['image']['contextLink']) $info .= "\nСсылка на сайт ".$arInfo['image']['contextLink'];
			$telegram->answerCallbackQuery([
					'callback_query_id' => $result["callback_query"]['id'],
					'text' 			=> 'Отправить в группе /sendpic '.$id,
					'show_alert' 	=> false,
					'cache_time' 	=> 1
				]);
			$telegram->sendMessage([
				'chat_id' => $result['callback_query']['message']['chat']['id'],
				'parse_mode'=> 'HTML',
				'disable_web_page_preview'=> true,
				'disable_notification' => true,
				'text' => "INFO:\n".$info
			]);
		}else{
			//всплывающее сообщение
			$telegram->answerCallbackQuery([
					'callback_query_id' => $result["callback_query"]['id'],
					'text' 			=> 'Ok 👌',
					'show_alert' 	=> false,
					'cache_time' 	=> 1
				]);

			//новые кнопки
	    	$keyboard = Main::reitingBtns($id, $from);
			$inlineKeyboardMarkup = array('inline_keyboard' => $keyboard);
			$telegram->editMessageReplyMarkup([
		        'chat_id' => $result['callback_query']['message']['chat']['id'],
		        'message_id' => $result['callback_query']['message']['message_id'],
		        'reply_markup' => json_encode($inlineKeyboardMarkup),
	        ]);
		}

        // $telegram->sendMessage([ 'chat_id' => '153057273', 'parse_mode'=> 'HTML', 'text' => "file_id ".json_encode($result['callback_query']) ]);
	}
}



$keyboard = [
	["/gif", "/pic", "/rdm", "/game", "/help", '/X']
];

$double_commands=explode(" ", $text);


if($text){
	Main::User($result["message"]["from"]); //сохраняем пользователя


	if ($text == "/start" || $text == "/start@tigif_bot") {
		$reply = "Привет ".$name." добро пожаловать в бота!";
		$reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false, "selective"=>false, "single_use"=>true ]);
	}
	elseif ($text == "/help" || $text == "/help@tigif_bot") {
		$reply = "\n
		/start - обновить бота
		/help - помощь
		/gif - получить гифку
		/pic - получить картинку
		/ngif  - только новые гифки
		/npic  - только новые картинки
		/rdm - случайно картинка или гифка
		/addgif ССЫЛКА - добавить в бота gif
		/addpic ССЫЛКА - добавить в бота pic
		/statistic - Статистика
		/tipost - #Сиськопост
		/sendpic ID - показать картинку/гифку по id
		/game - пошлая цитата
		/X - скрыть клавиатуру чтобы она снова отобразилась нажмите /start
		";
	}
	elseif ($text == "/statistic" || $text == "/statistic@tigif_bot") {
		$stat = Main::getFullStatistic();
		$telegram->sendPhoto([
			'chat_id' => $chat_id,
			'photo'=> 'https://sasha23.tmweb.ru/assets/diagramm.php?gif='.$stat[0].'&pic='.$stat[1],
			'caption' => "
				Всего: ".$stat[2]."\n
				💛 GIF: ".$stat[0]."
				💜 PIC: ".$stat[1]."\n
				Пользователей: ".Main::FindAllUsers()
		]);
	}
	elseif ($text == "/tipost" || $text == "/tipost@tigif_bot") {
		$get = Main::getRandImages();
		foreach ($get as $key => $img) {
			if ($key==0) {
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?$img['FILE_ID']:$img['URL'], "caption" => "#сиськопост");
			}
			else{
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?$img['FILE_ID']:$img['URL']);
			}
		}
		$telegram->sendmediagroup(['chat_id' => $chat_id, 'media' => json_encode($images)]);
	}
	elseif ($text == "/gif" || $text == "/gif@tigif_bot" || $text == "/pic@tigif_bot" || $text == "/pic" || $text == '/ngif' || $text == '/npic') {
		if     ($text == "/gif" || $text == "/gif@tigif_bot" ) {$img = Main::getSingleImage("gif");}
		elseif ($text == "/pic" || $text == "/pic@tigif_bot" ){$img = Main::getSingleImage("pic");}
		elseif ($text == '/ngif') {$img = Main::getSingleImageNEW("gif");}
        elseif ($text == '/npic') {$img = Main::getSingleImageNEW("pic");}

		getIMG_send($telegram, $chat_id, $img);
	}
	elseif ($text == "/game" || $text == "/game@tigif_bot"){
		$img = Main::DBrandomContent();
		$pic_id = $img['ID'];
		$user = $result["message"]["from"]['username'];
		$caption = "@".$user." ".Main::getGameText();
		if ($img['TYPE'] == "GIF") {
			$telegram->sendAnimation([
				'chat_id' 		=> $chat_id,
				'animation'		=> ($img['FILE_ID'])?$img['FILE_ID']:$img['URL'],
				'caption'		=> $caption,
			]);
		}else{
            //sendpic($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption);
			$telegram->sendPhoto([
				'chat_id' 	=> $chat_id,
				'photo'		=> ($img['FILE_ID'])?$img['FILE_ID']:$img['URL'],
				'caption'		=> $caption,
			]);
		}
	}
    elseif ( $text == '/rdm' ){
        $r = rand(0,1);

        switch ($r){
            case 0:
                gif($telegram, $chat_id);
                break;
            case 1:
                pic($telegram, $chat_id);
                break;
        }

    }
    elseif ( $text == '/X' ){
        $reply_markup = json_encode(['remove_keyboard' => true]);
        $arAns = [ 'chat_id' => $chat_id, 'text' => 'Кнопки отключены', 'reply_markup' => $reply_markup ];
		$telegram->sendMessage($arAns);
    }


	//Добавление материала
	/*elseif ($double_commands[0] == '/addgif') {
		$add = Main::addImage('gif', trim($double_commands[1]));
		if ($add == 1) {
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => "Файл добавлен");
		}else{
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: Файл уже есть']);
		}
	}
	elseif ($double_commands[0] == '/addpic') {
		$add = Main::addImage('pic', trim($double_commands[1]));
		if ($add == 1) {
			$telegram->sendPhoto([ 'chat_id' => $chat_id,'photo'=> $double_commands[1]]);
		}else{
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: Файл уже есть']);
		}
	}*/
	elseif ($double_commands[0] == '/sendpic') {
        $id = intval($double_commands[1]);
		$img = Main::getImageById($id);
		getIMG_send($telegram, $chat_id, $img);
	}

	//обычные ответы
	if ($reply) {
		$arAns = [ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply];
		if ($reply_markup) $arAns['reply_markup']=$reply_markup;
		$telegram->sendMessage($arAns);
	}
}

function gif($telegram, $chat_id){
    $img = Main::getSingleImage("gif");
    getIMG_send($telegram, $chat_id, $img);
}
function pic($telegram, $chat_id){
    $img = Main::getSingleImage("pic");
    getIMG_send($telegram, $chat_id, $img);
}
function ngif($telegram, $chat_id){
    $img = Main::getSingleImageNEW("gif");
    getIMG_send($telegram, $chat_id, $img);
}
function npic($telegram, $chat_id){
    $img = Main::getSingleImageNEW("pic");
    getIMG_send($telegram, $chat_id, $img);
}

function getIMG_send($telegram, $chat_id, $img){
    $pic_id = $img['ID'];
    $result = $telegram->getWebhookUpdates();
    $from = $result["message"]["from"]['id'];

	$keyboard = Main::reitingBtns($pic_id, $from);
	$inlineKeyboardMarkup = array('inline_keyboard' => $keyboard);

	if ($img['TYPE'] == "GIF") { sendgif($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id); }
    if ($img['TYPE'] == "PIC") { sendpic($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id); }
    //if ($img['TYPE'] == "MOV") { /*senmov*/ }
}

function sendgif($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption = ''){
    if (!$img){
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
        return;
    }
//    $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "открыть " . $img." id ".$pic_id]);
    $response = $telegram->setAsyncRequest(false)->sendAnimation([
		'chat_id' 	=> $chat_id,
		'animation'		=> ($img['FILE_ID'])?$img['FILE_ID']:$img['URL'],
        'caption'   =>$caption,
		'reply_markup' => json_encode($inlineKeyboardMarkup)
	]);
    $ans = var_dump($response, true);
    $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "new idgif " . $ans]);
    Main::setViewCount($pic_id);
    $ans = $response["photo"][0]['file_id'];
    if (!$img['FILE_ID']) {
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "new idgif " . $ans]);
         if ($ans) Main::setImageFileID($pic_id, $ans);
    }

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . $img['URL']." id ".$pic_id]);
    }
    //if ($ans) Main::setViewCount($pic_id);
}

function sendpic($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption = ''){
    if (!$img){
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
        return;
    }
    /*if (!$img['FILE_ID']){
        //Main::setDeactive($pic_id);
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Ошибка :("]);
    }*/
    $response = $telegram->setAsyncRequest(false)->sendPhoto([
		'chat_id' 	=> $chat_id,
		'photo'		=> ($img['FILE_ID'])?$img['FILE_ID']:$img['URL'],
        'caption'   =>$caption,
		'reply_markup' => json_encode($inlineKeyboardMarkup)
	]);

    $ans = $response["photo"][0]['file_id'];
    if (!$img['FILE_ID']) {
         if ($ans) Main::setImageFileID($pic_id, $ans);
    }

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . $img['URL']." id ".$pic_id]);
    }

    if ($ans) Main::setViewCount($pic_id);
}
