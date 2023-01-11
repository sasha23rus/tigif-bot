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

/*if ($result){
    $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "принял " . json_encode($result) ]);
    $count = count($result['message']['photo'])-1;
    $test = $telegram->setAsyncRequest(false)->getFile(['file_id' => $result['message']['photo'][$count]['file_id'] ]);
    //Сохранить файл
    $url = 'https://api.telegram.org/file/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$test['file_path'];
    $path = $_SERVER['DOCUMENT_ROOT'] . '/upload/'.$test['file_path'];
    if(file_put_contents($path, file_get_contents($url))){
        $telegram->sendMessage(['chat_id' => '153057273', 'text' => "Успешно загружен" ]);
    }

    $telegram->sendMessage(['chat_id' => '153057273', 'text' => "getFile  " . json_encode($test) ]);
}*/

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
                    'sendAnimation', ['chat_id' => $chat_id, 'animation' => ($img['FILE_ID'])?:$img['URL'], 'caption'=> $icon." место" ]
                );
            }
        }
        if ($action == 'pic_all'){
            $imgList = Main::getTopAll('PIC');
            foreach ($imgList as $key => $img){
                /*$response = $telegram->setAsyncRequest(false)->uploadFile(
                    'sendAnimation', ['chat_id' => $from, 'animation' => ($img['FILE_ID'])?:$img['URL'], 'caption'=> "🏆 первое место" ]
                );
                if ($key > 0){
                }*/
				    $images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL']);
            }
    		$telegram->sendmediagroup(['chat_id' => $chat_id, 'media' => json_encode($images)]);

        }
    }
}

$keyboard = [
	["/gif", "/pic", "/rdm", "/game", "/help", '/X']
];

$double_commands=explode(" ", $text);

if($text){

	if($text == "/start"   || $text == "/start@tigif_bot") {
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
	elseif ($text == "/help"    || $text == "/help@tigif_bot") {
		$reply = "\n
		/start - обновить бота
		/help - помощь
		/gif - получить гифку
		/pic - получить картинку
		/mov - получить видео
		/ngif  - только новые гифки
		/npic  - только новые картинки
		/rdm - случайно картинка или гифка
		/add ССЫЛКА - добавить в бота gif/jpg/jpeg/png/mp4
		/statistic - Статистика
		/tipost - #Сиськопост
		/sendpic ID - показать картинку/гифку по id
		/game - пошлая цитата
		/top - Лучшие по голосованию
		/X - скрыть клавиатуру чтобы она снова отобразилась нажмите /start
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
		foreach ($get as $key => $img) {
			if ($key==0) {
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL'], "caption" => "#сиськопост");
			}
			else{
				$images[] = array('type'=>'photo', 'media'=>($img['FILE_ID'])?:$img['URL']);
			}
		}
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
		$caption = "@".$user." ".Main::getGameText();
        tg::localSend($img, $result, $telegram, '', $caption);
	}
    elseif ( $text == '/id'      || $text == "/id@tigif_bot" ){
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'ID: '.$uid."\nChat: ".$chat_id ]);
    }
    elseif ( $text == '/X'      || $text == "/X@tigif_bot" ){
        $reply_markup = json_encode(['remove_keyboard' => true]);
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => 'Кнопки отключены', 'reply_markup' => $reply_markup ]);
    }


	//Добавление материала
    elseif ($double_commands[0] == '/add'){
        $validUrl = filter_var($double_commands[1], FILTER_VALIDATE_URL);
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
            }
            if ($type){
                $add = Main::addImage($type, trim($double_commands[1]));
                if ($add > 0) {
                    $img = Main::getImageById($add);
                    getIMG_send($telegram, $chat_id, $img);
                }else{
                    $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: Файл уже есть']);
                }
            }
        }
    }
	elseif ($double_commands[0] == '/sendpic') {
        $id = intval($double_commands[1]);
		$img = Main::getImageById($id);
        //$telegram->sendMessage(['chat_id' => '153057273', 'text' => $img['ID']." ngif \n" . $img['URL']]);
		getIMG_send($telegram, $chat_id, $img);
	}

	//обычные ответы
	if ($reply) {
		$arAns = [ 'chat_id' => $chat_id, 'text' => $reply];
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
//    $img = Main::top();
//    return tg::localSend($img, $result, $telegram, 'rdm');
}
function rdm($telegram): ?bool
{
    global $result;
    $img = Main::DBrandomContent();
    return tg::localSend($img, $result, $telegram, 'rdm');
}
function gif($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImage("gif");
    return tg::localSend($img, $result, $telegram, 'gif');
}
function pic($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImage("pic");
    return tg::localSend($img, $result, $telegram, 'pic');
}
function ngif($telegram): ?bool
{
    global $result;
    $img = Main::getSingleImageNEW("gif");
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

    $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . json_encode($response) ]);

    if (!$img['FILE_ID'] && !$ans){
         $telegram->sendMessage(['chat_id' => '153057273', 'parse_mode' => 'HTML', 'text' => "Не смог открыть " . $img['URL'] ]);
    }

    if ($ans) Main::setViewCount($pic_id);
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
