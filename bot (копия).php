<?
include_once('vendor/autoload.php');
foreach (glob( "vendor/krugozor/database/src/*.php") as $filename) {
    require_once $filename;
}
include_once('lib/main.php');

use Telegram\Bot\Api;
use Lib\Main;

$telegram = new Api('5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE', true);

$result = $telegram->getWebhookUpdates();
$text = $result["message"]["text"];
$chat_id = $result["message"]["chat"]["id"];
$name = $result["message"]["from"]["username"];

/*$answer = $telegram->answerCallbackQuery();

if ($result->isType('callback_query')) {
	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => json_encode($result)]);
}*/

$keyboard = [
	["/gif", "/pic", "/start", "/help"]
];

$double_commands=explode(" ", $text);


if($text){
	if ($text == "/start") {
		$reply = "Привет ".$name." добро пожаловать в бота!";
		$reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
	}
	elseif ($text == "/help") {
		$reply = "/start - обновить бота\n
		/id - получить свой id\n
		/help - помощь\n
		/gif - получить гифку\n
		/pic - получить картинку\n
		/addgif ССЫЛКА - добавить в бота gif\n
		/addpic ССЫЛКА - добавить в бота pic\n
		/statistic - Статистика
		";
	}
	elseif ($text == "/statistic") {
		$stat = Main::getFullStatistic();
		// $img = Main::diagramm($stat[0], $stat[1]);
		// $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => "Всего: ".$stat[2]."\nGIF: ".$stat[0]."\nPIC: ".$stat[1]]);
		$telegram->sendPhoto([ 'chat_id' => $chat_id,'photo'=> 'https://sasha23.tmweb.ru/assets/diagramm.php?gif='.$stat[0].'&pic='.$stat[1], 'caption' => "Всего: ".$stat[2]."\nGIF: ".$stat[0]."\nPIC: ".$stat[1] ]);
	}
	elseif ($text == "/gif" || $text == "/gif@tigif_bot" || $text == "/pic@tigif_bot" || $text == "/pic") {
		//получить картинку
		if ($text == "/gif" || $text == "/gif@tigif_bot" ) {
			$has_gif = Main::getSingleImage("gif");
			if (!is_array($has_gif)) {
				$telegram->sendAnimation([ 'chat_id' => $chat_id, 'animation'=> (String) $has_gif]);
				$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $has_gif]);
			}else{
				$reply = $has_gif['error'];
			}
			// $reply = $has_gif;
		}else{
			$has_img = Main::getSingleImage("pic");
			if (!is_array($has_img)) {
				$picid = 'test';
				$keyboard = array(
			        array(
			          array(
			          	'text'=>'👎',
			          	'callback_data'=> "1",
			          	  /*array(
				          	'id'=> $result["message"]["id"],
				          	'from'=> $result["message"]["from"]['id'],
				          	'message'=>'test'
				          )*/

			          ),
			          array('text'=>'🚫','callback_data'=>"2"),
			          array('text'=>'👍','callback_data'=>"1")
			        )
			    );

			    $inlineKeyboardMarkup = array(
			      'inline_keyboard' => $keyboard
			    );

				$telegram->sendPhoto([ 
					'chat_id' 	=> $chat_id,
					'photo'		=> $has_img,
					'reply_markup' => json_encode($inlineKeyboardMarkup)
				]);
				// $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => json_encode($result)]);
			}else{
				$reply = $has_gif['error'];
			}
		}
	}
	elseif ($double_commands[0] == '/addgif') {
		$add = Main::addImage('gif', trim($double_commands[1]));
		if ($add == 1) {
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $double_commands[1]]);
		}else{
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: '.$add]);
		}
	}
	elseif ($double_commands[0] == '/addpic') {
		$add = Main::addImage('pic', trim($double_commands[1]));
		if ($add == 1) {
			$telegram->sendPhoto([ 'chat_id' => $chat_id,'photo'=> $double_commands[1]]);
		}else{
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => 'ошибка: '.$add]);
		}
	}
	else{
		//$reply = "none";
	}



	//обычные ответы
	if ($reply) {
		$arAns = [ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply];
		if ($reply_markup) $arAns['reply_markup']=$reply_markup; 
		$telegram->sendMessage($arAns);
	}
	

}
/*else{
	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => json_encode($result)]);
}*/