<?
include_once($_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php');
foreach (glob( "vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/main.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/myTG.php');

use Telegram\Bot\Api;
use Lib\Main;
use my\tg;

$telegram = new Api('5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE', true);

$result = $telegram->getWebhookUpdates();
$text = $result["message"]["text"];
$chat_id = $result["message"]["chat"]["id"];
$name = $result["message"]["from"]["username"];
$uid = $result["message"]["from"]["id"];


//$telegram->sendMessage(['chat_id' => '153057273', 'text' => "Успешно загружен" ]);
//die();

	//$telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "принял " . json_encode($result) ]);
if ($result["message"]["chat"]["type"]=="private"){
	//бот принимает файлы
	if ($result["message"]["video"]){
		$test = $telegram->setAsyncRequest(false)->getFile(['file_id' => $result['message']['video']['file_id'] ]);
		$file_from_tgrm = 'https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$test['file_path'];
		$path = $_SERVER["DOCUMENT_ROOT"].'/upload/'.stripcslashes($test['file_path']);
		$move = copy($file_from_tgrm, $path);
		//$telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "файл " . $file_from_tgrm ]);
		//$telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "original_puth " . $siteurl ]);

		if($move){
			//$telegram->sendMessage(['chat_id' => '153057273', 'text' => "Успешно загружен" ]);
			//$telegram->sendMessage(['chat_id' => '153057273', 'text' => "getFile  " . json_encode($test) ]);
			$sendresult = array(
				"info"=>array(
					"title"=>"Добавлено пользователем",
					"link" => "https://ti-bot.ru/upload/".stripcslashes($test['file_path'])
				),
				"user_name" => $name,
				"user_uid" => $uid,
				"file_path" => '/upload/'.stripcslashes($test['file_path']),
				"type" => 'video',
				"thumb"=>$result['message']['video']['thumb']["file_id"],
				"file_id"=>$result['message']['video']["file_id"],
				"file_unique_id"=>$result['message']['video']["file_unique_id"],
				"file_name"=>$result['message']['video']["file_name"],
				"mime_type"=>$result['message']['video']["mime_type"],
				"file_size"=>$result['message']['video']["file_size"],
			);
			$id = Main::addContent("MOV", $sendresult);
			$img = Main::getImageById($id);
			getIMG_send($telegram, $chat_id, $img);//отправляем результат
		}else{
			$telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Ошибка сохранения ".json_encode($move) ]);
		}
	}
	if ($result["message"]["animation"]){
		$test = $telegram->setAsyncRequest(false)->getFile(['file_id' => $result['message']['animation']['file_id'] ]);
		$file_from_tgrm = 'https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$test['file_path'];
		$path = $_SERVER["DOCUMENT_ROOT"].'/upload/'.stripcslashes($test['file_path']);
		$move = copy($file_from_tgrm, $path);
		if($move){
			$sendresult = array(
				"info"=>array(
					"title"=>"Добавлено пользователем",
					"link" => "https://ti-bot.ru/upload/".stripcslashes($test['file_path'])
				),
				"user_name" => $name,
				"user_uid" => $uid,
				"file_path" => '/upload/'.stripcslashes($test['file_path']),
				"type" => 'animation',
				"thumb"=>$result['message']['animation']['thumb']["file_id"],
				"file_id"=>$result['message']['animation']["file_id"],
				"file_unique_id"=>$result['message']['animation']["file_unique_id"],
				"file_name"=>$result['message']['animation']["file_name"],
				"mime_type"=>$result['message']['animation']["mime_type"],
				"file_size"=>$result['message']['animation']["file_size"],
			);
			$id = Main::addContent("GIF", $sendresult);
			$img = Main::getImageById($id);
			getIMG_send($telegram, $chat_id, $img);//отправляем результат
		}else{
			$telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Ошибка сохранения ".json_encode($move) ]);
		}
	}
	if ($result["message"]["photo"]){
		$file_id = $result["message"]["photo"][count($result["message"]["photo"]) - 1]["file_id"];
		$test = $telegram->setAsyncRequest(false)->getFile(['file_id' => $file_id ]);
		$file_from_tgrm = 'https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$test['file_path'];
		$path = $_SERVER["DOCUMENT_ROOT"].'/upload/'.stripcslashes($test['file_path']);
		$move = copy($file_from_tgrm, $path);
		if($move){
			$sendresult = array(
				"info"=>array(
					"title"=>"Добавлено пользователем",
					"link" => "https://ti-bot.ru/upload/".stripcslashes($test['file_path'])
				),
				"user_name" => $name,
				"user_uid" => $uid,
				"file_path" => '/upload/'.stripcslashes($test['file_path']),
				"type" => 'photo',
				"thumb"=>$result['message']['photo'][0]["file_id"],
				"file_id"=>$file_id,
				"file_unique_id"=>$result['message']['photo'][count($result["message"]["photo"]) - 1]["file_unique_id"],
			);
			$id = Main::addContent("PIC", $sendresult);
			$img = Main::getImageById($id);
			getIMG_send($telegram, $chat_id, $img);//отправляем результат
		}else{
			$telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Ошибка сохранения ".json_encode($move) ]);
		}
	}
	
	//бот принимает ссылки
	if ($result["message"]["entities"]){
		$urlList = explode("\n", $result["message"]['text']);
		/*Цикл не работает телега режет, поэтому добавляется только первый*/
		foreach ($result["message"]["entities"] as $key => $type){
			if($type["type"] == 'url'){
				$url = stripcslashes($urlList[$key]);
				if(addcontentbyurl($url, $telegram, $chat_id)) continue;
			}
		}
	}
	
}


if($result["callback_query"]){

    //$telegram->sendMessage(['chat_id' => '153057273', 'text' => "test  " . json_encode($result["callback_query"]) ]);
	$callback_data = explode('|',$result["callback_query"]['data']);
    $from = $result["callback_query"]["from"]["id"];
    $chat_id = $result['callback_query']['message']['chat']['id'];
	if ($callback_data[0]=="reitng") {
		$id = $callback_data[1];
		$action = $callback_data[2];
		$file = $result["callback_query"]['message']['photo'];
		if (!is_array($file)) { $file_id = $result["callback_query"]['message']['animation']['file_id']; }
        if (!is_array($file)) { $file_id = $result["callback_query"]['message']['video']['file_id']; }
        else{ $file_id = $file[0]['file_id']; }

        if ($action=='removenow'){
            Main::setDeactive($id);
            $telegram->answerCallbackQuery([
                'callback_query_id' => $result["callback_query"]['id'],
                'text' 			=> 'Удалено',
                'show_alert' 	=> false,
                'cache_time' 	=> 1
            ]);
        }
        //проверка, голосовал ли пользователь
        if ($action!='info' &&  $action!='removenow') {
            if(Main::CheckReiting($id, $from) > 0){
                //всплывающее сообщение
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $result["callback_query"]['id'],
                    'text' 			=> 'Уже проголосовал',
                    'show_alert' 	=> false,
                    'cache_time' 	=> 1
                ]);
            }else{
                Main::setReiting($id, $from, $action, $file_id);
                //всплывающее сообщение
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $result["callback_query"]['id'],
                    'text' 			=> 'Ok 👌',
                    'show_alert' 	=> false,
                    'cache_time' 	=> 1
                ]);

                //новые кнопки
                $keyboard = Main::reitingBtns($id);
                $inlineKeyboardMarkup = array('inline_keyboard' => $keyboard);
                $telegram->editMessageReplyMarkup([
                    'chat_id' => $result['callback_query']['message']['chat']['id'],
                    'message_id' => $result['callback_query']['message']['message_id'],
                    'reply_markup' => json_encode($inlineKeyboardMarkup),
                ]);
            }
        }

        if ($action=='info') {
            $img = Main::getImageById($id);
            $arInfo = unserialize($img['INFO']);

            $info = "Ссылка на этот контент /sendpic_".$id;
            if ($arInfo['title'])   $info .= "\n".$arInfo['title'];
            if ($arInfo['link'])    $info .= "\nСсылка на оригинал ".$arInfo['link'];
			
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
        }
	}
    if ($callback_data[0]=="top") {
        $action = $callback_data[1];
        if ($action == 'gif_all'){
            $imgList = Main::getTopAll('GIF');
            foreach ($imgList as $key => $img){
                if ($key == 0 ) $icon = "🥇";
                if ($key == 1 ) $icon = "🥈";
                if ($key == 2 ) $icon = "🥉";
                $response = $telegram->setAsyncRequest(false)->uploadFile(
                    'sendAnimation', ['chat_id' => $chat_id, 'animation' => ($img['FILE_ID'])?:$img['URL'], 'caption'=> $icon." место /sendpic_".$img['ID'] ]
                );
            }
        }
        if ($action == 'pic_all'){
            $imgList = Main::getTopAll('PIC');
			$caption = '';
            foreach ($imgList as $key => $img){
				if ($key == 0 ) $icon = "🥇";
                if ($key == 1 ) $icon = "🥈";
                if ($key == 2 ) $icon = "🥉";
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL']);
				$caption .= $icon." /sendpic_".$img['ID']." ";
            }
			$images[$key]['caption'] = $caption;
    		$telegram->sendmediagroup(['chat_id' => $chat_id, 'media' => json_encode($images)]);
        }
		
		if ($action == 'gif_7'){
            $imgList = Main::getTopWeek('GIF');
            foreach ($imgList as $key => $img){
                if ($key == 0 ) $icon = "🥇";
                if ($key == 1 ) $icon = "🥈";
                if ($key == 2 ) $icon = "🥉";
                $response = $telegram->setAsyncRequest(false)->uploadFile(
                    'sendAnimation', ['chat_id' => $chat_id, 'animation' => ($img['FILE_ID'])?:$img['URL'], 'caption'=> $icon." место /sendpic_".$img['ID'] ]
                );
            }
        }
		
		if ($action == 'pic_7'){
            $imgList = Main::getTopWeek('PIC');
			$caption = '';
            foreach ($imgList as $key => $img){
				if ($key == 0 ) $icon = "🥇";
                if ($key == 1 ) $icon = "🥈";
                if ($key == 2 ) $icon = "🥉";
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL']);
				$caption .= $icon." /sendpic_".$img['ID']." ";
            }
			$images[$key]['caption'] = $caption;
    		$telegram->sendmediagroup([ 'chat_id' => $chat_id, 'media' => json_encode($images) ]);
        }
    }
	if ($callback_data[0]=="next") {
		$now_id = $callback_data[1];
		$img = Main::getNextContent($now_id);
		getIMG_send($telegram, $chat_id, $img);
	}
}

$keyboard = [
	["/gif", "/pic", "/mov", "/game", "/tipost", "/top", "❓", "▶", '✖']
];

$double_commands=explode(" ", $text);
$new_double_commands=explode("_",$text);

if($text){

	if($text == "/start"   		|| $text == "/start@tigif_bot"	|| $text == '▶') {
	    Main::User($result["message"]["from"]); //сохраняем пользователя
		$reply = "Привет ".$name." добро пожаловать в бота!";
		$reply_markup = $telegram->replyKeyboardMarkup(
            [
                'keyboard' => $keyboard,
                'resize_keyboard' => true, //адекватные маленькие кнопки
                'one_time_keyboard' => false, //кнопки не пропадают после вызова команды
                //"selective"=>true, //так и не понял
            ]
        );
	}
	elseif ($text == "/help"    || $text == "/help@tigif_bot"	|| $text == '❓') {
		$reply = "
			<b>Комманды:</b>
			/start - обновить бота
			/help - помощь
			/gif - получить гифку
			/pic - получить картинку
			/mov - получить видео
			/rdm - случайно картинка или гифка
			/tipost - #Сиськопост
			/sendpic ID - показать картинку/гифку по id
			/game - пошлая цитата
			/top - Лучшие по голосованию
			/X - скрыть клавиатуру, чтобы она снова отобразилась нажмите /start
			
			<b>*Передать контент в бота*</b>
			В приват сообщениях боту
			
			<u>файлы:</u>
			Бот может принимть mp4 файлы
			Бот может принимает gif файлы
			Бот может принимает изображения
			В личку можно кидать файлы по одному или несколько сразу
			файлы размером до 20 мб
			
			<u>ссылки:</u>
			В личку можно передать ссылку на gif/jpg/jpeg/png/mp4
			по одной ссылке за раз
			Максимальный размер файла 50мб
			
			<code>Или можно переслать боту сообщение из другого чата с контентом</code>
		";
	}
	elseif ($text == "/statistic"|| $text == "/statistic@tigif_bot") {
		$stat = Main::getFullStatistic();
		$telegram->sendPhoto([
			'chat_id' => $chat_id,
			'photo'=> 'https://sasha23.tmweb.ru/assets/diagramm.php?gif='.$stat[0].'&pic='.$stat[1].'&mov='.$stat[2],
			'caption' => "
				Всего: ".$stat[3]."\n
				💛 GIF: ".$stat[0]."
				💜 PIC: ".$stat[1]."
				❤️ MOV: ".$stat[2]."\n
				Пользователей: ".Main::FindAllUsers()
		]);
	}
	elseif ($text == "/tipost"  || $text == "/tipost@tigif_bot") {
		$get = Main::getRandImages();
        $images = array();
		$caption = "#сиськопост \nСcылки:\n";
		foreach ($get as $key => $img) {
			$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL']);
			$caption .= '/sendpic_'.$img['ID']."\n";
		}
		$images[$key]['caption'] = $caption;
		$telegram->sendmediagroup(['chat_id' => $chat_id, 'media' => json_encode($images)]);
	}
	elseif ($text == "/gif"     || $text == "/gif@tigif_bot" ) { gif($telegram); }
	elseif ($text == "/pic"     || $text == "/pic@tigif_bot" ) { pic($telegram); }
    elseif ($text == "/mov"     || $text == "/mov@tigif_bot" ) { mov($telegram); }
	elseif ($text == '/ngif'    || $text == "/ngif@tigif_bot") { ngif($telegram);}
    elseif ($text == '/npic'    || $text == "/npic@tigif_bot") { npic($telegram);}
    elseif ($text == '/rdm'     || $text == "/rdm@tigif_bot" ) { rdm($telegram); }
    elseif ($text == "/top"     || $text == "/top@tigif_bot")  { top($telegram); }
	elseif ($text == "/game"    || $text == "/game@tigif_bot") {
		$img = Main::DBrandomContent();
		$pic_id = $img['ID'];
		$user = $result["message"]["from"]['username'];
		$caption = "@".$user." ".Main::getGameText()."\n/sendpic_".$pic_id;
        tg::localSend($img, $result, $telegram, '', $caption);
	}
    elseif ( $text == '/id'     || $text == "/id@tigif_bot" ){
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'ID: '.$uid."\nChat: ".$chat_id ]);
    }
    elseif ( $text == '/X'      || $text == "/X@tigif_bot"		|| $text == '✖'){
        $reply_markup = json_encode(['remove_keyboard' => true]);
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Кнопки отключены', 'reply_markup' => $reply_markup ]);
    }
	
	elseif ($double_commands[0] == '/sendpic') {
        $id = intval($double_commands[1]);
		$img = Main::getImageById($id);
		getIMG_send($telegram, $chat_id, $img);
	}
	elseif ($new_double_commands[0] == '/sendpic') {
		$id = intval($new_double_commands[1]);
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

function top($telegram){
    global $result;
    $chat_id = $result["message"]["chat"]["id"];
    $inlineKeyboardMarkup = array('inline_keyboard' => Main::TopBtns());
    $arAns = [ 'chat_id' => $chat_id, 'text' => "Лучшее из того что мы сами выбрали", 'reply_markup'=>json_encode($inlineKeyboardMarkup) ];
    $telegram->sendMessage($arAns);
}
function rdm($telegram, $repeat = 0): ?bool
{
    global $result;
    $img = Main::DBrandomContent($repeat);
    return tg::localSend($img, $result, $telegram, 'rdm');
}
function gif($telegram, $repeat = 0): ?bool
{
    global $result;
    $img = Main::getSingleImage("gif", $repeat);
    return tg::localSend($img, $result, $telegram, 'gif');
}
function pic($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImage("pic");
    return tg::localSend($img, $result, $telegram, 'pic');
}
function ngif($telegram, $repeat = 0): ?bool
{
    global $result;
    $img = Main::getSingleImageNEW("gif", $repeat);
    return tg::localSend($img, $result, $telegram, 'ngif');
}
function npic($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImageNEW("pic");
    return tg::localSend($img, $result, $telegram, 'npic');
}
function mov($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImage("mov");
    return tg::localSend($img, $result, $telegram, 'mov');
}

function getIMG_send($telegram, $chat_id, $img): void
{
    $pic_id = $img['ID'];
    $result = $telegram->getWebhookUpdates();
    $from = $result["message"]["from"]['id'];
	if(!$from) $from  = $result['callback_query']["from"]['id'];

    $keyboard = Main::reitingBtns($pic_id);
    if($from == 153057273) $keyboard[] = Main::AdminBtns($pic_id);
	
	$inlineKeyboardMarkup = array('inline_keyboard' => $keyboard);


	if ($img['TYPE'] == "GIF") { sendgif($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id); }
    if ($img['TYPE'] == "PIC") { sendpic($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id); }
    if ($img['TYPE'] == "MOV") { sendmov($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id); }
}

function sendgif($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption = ''): void
{
    if (!$img){
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
        return;
    }

    $response = $telegram->setAsyncRequest(false)->uploadFile(
        'sendAnimation',
        [
            'chat_id' 	=> $chat_id,
            'animation'	=> ($img['FILE_ID'])?:$img['URL'],
            'caption'   =>$caption,
            'reply_markup' => json_encode($inlineKeyboardMarkup)
	    ]
    );

    Main::setViewCount($pic_id);
    $ans = $response["animation"]['file_id'];
    if (!$img['FILE_ID']) {
         if ($ans) Main::setImageFileID($pic_id, $ans);
         $telegram->sendMessage(['chat_id' => '153057273', 'text' => "new idgif " . $ans]);
    }

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Не смог открыть id #".$pic_id."\n".$img['URL']]);
    }
    if ($ans) Main::setViewCount($pic_id);
}
function sendpic($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption = ''): void
{
    $result = $telegram->getWebhookUpdates();
    if (!$img){
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
        return;
    }

    $response = $telegram->setAsyncRequest(false)->sendPhoto([
		'chat_id' 	=> $chat_id,
		'photo'		=> ($img['FILE_ID'])?:$img['URL'],
        'caption'   =>$caption,
		'reply_markup' => json_encode($inlineKeyboardMarkup)
	]);

    $count = count($result['message']['photo'])-1;
    $ans = $response["photo"][$count]['file_id'];
    if (!$img['FILE_ID']) {
         if ($ans) Main::setImageFileID($pic_id, $ans);
    }

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . $img['URL']." id ".$pic_id]);
    }

    if ($ans) Main::setViewCount($pic_id);
}
function sendmov($telegram, $chat_id, $inlineKeyboardMarkup, $img, $pic_id, $caption = ''){
    $result = $telegram->getWebhookUpdates();
    if (!$img){
        $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
        return;
    }
    $response = $telegram->setAsyncRequest(false)->sendVideo([
		'chat_id' 	=> $chat_id,
		'video'		=> ($img['FILE_ID'])?:$img['URL'],
        'caption'   =>$caption,
		'reply_markup' => json_encode($inlineKeyboardMarkup)
	]);
    $ans = $response["video"]['file_id'];
    if (!$img['FILE_ID']) {
         if ($ans) Main::setImageFileID($pic_id, $ans);
    }

    //$telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . json_encode($response) ]);

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . $img['URL'] ]);
    }

    if ($ans) Main::setViewCount($pic_id);
}

function addcontentbyurl($url, $telegram, $chat_id) {
	$validUrl = filter_var($url, FILTER_VALIDATE_URL);
	$type = false;
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
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'ошибка: Формат файла не поддерживается']);
			return false;
		}
		if ($type){
			$add = Main::addImage($type, trim($url));
			if ($add > 0) {
				$img = Main::getImageById($add);
				getIMG_send($telegram, $chat_id, $img);
				return true;
			}else{
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: Файл уже есть']);
				return false;
			}
		}
	}else{
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: URL не подходит :(']);
		return false;
	}
	
}

/*Примеры*/

/**
 * отправить сообщение
 * https://api.telegram.org/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/sendMessage?chat_id=153057273&text=test
 *
 * отправить фотао
 * https://api.telegram.org/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/sendPhoto?chat_id=153057273&photo=AgACAgIAAxkDAAIM22ORiiTKMv0M1xcGKK8YJ1s7C6a5AAJSwTEb9jmASKZGIPX7raXwAQADAgADcwADKwQ
 *
 * установить webhook
 * https://api.telegram.org/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/setWebhook?url=ti-bot.ru/bot.php
 *
 * GIF
 * https://api.telegram.org/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/sendAnimation?chat_id=153057273&animation=http://pornomig.net/data/uploads/2019-02-04/images/pornomig_1103.gif
 *
 * //test
 * https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/https://cams.place/uploads/n/naugthycouple1010/naugthycouple1010_5.gif


 */
