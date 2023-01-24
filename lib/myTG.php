<?php
namespace my;

use Lib\Main;

class tg{
    public static function sendTG($endpoint, $params){
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot5924175794:AAG-kS9pkeulfOUAr69QoP6R2-tChx-yHXE/'.$endpoint);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

            $out = curl_exec($curl);
            $out = json_decode($out);

            curl_close($curl);

            return $out;
        }
    }

    public static function localSend($img, $result, $telegram, $repeat = '', $caption = ''){
        $pic_id  =  $img['ID'];
        $chat_id =  $result["message"]["chat"]["id"];

        //если результатов больше нету
        if (!$img){
            $telegram->sendMessage(['chat_id' => $chat_id, 'parse_mode' => 'HTML', 'text' => "Закончились :("]);
            return;
        }

        if ($repeat){
            $keyboard = Main::reitingBtns($pic_id);
            if($chat_id == 153057273) $keyboard[] = Main::AdminBtns($pic_id);
            $inlineKeyboardMarkup = array('inline_keyboard' => $keyboard);
        }

        if($img['TYPE']=='GIF'){
            $endpoint = 'sendAnimation';
            $type = 'animation';
        }
        if ($img['TYPE']=='PIC'){
            $endpoint = 'sendPhoto';
            $type = 'photo';
        }
        if ($img['TYPE']=='MOV'){
            $endpoint = 'sendVideo';
            $type = 'video';
        }

        $params = ['chat_id' => $chat_id, 'caption' =>$caption, $type => ($img['FILE_ID'])?$img['FILE_ID']:$img['URL']];
        if ($inlineKeyboardMarkup) $params['reply_markup'] = json_encode($inlineKeyboardMarkup);

        //отправка запроса
        $out = tg::sendTG($endpoint, $params);

        //DEBUG
        if (!$out->ok){
            $telegram->sendMessage([ 'chat_id' => '153057273', 'text' => $pic_id." Error \n" . $out->description ]);
        }

        //увеличение счётчика просмотра +1
        Main::setViewCount($pic_id);
        //обработка ответа
        if (!$img['FILE_ID']){

            //$telegram->sendMessage([ 'chat_id' => '153057273', 'text' => $pic_id." vardump \n" . json_encode($out) ]);
            if ($img['TYPE']=='PIC'){
                $arFile = (array) $out->result->photo;
                $count = count($arFile)-1;
                $file_id = $arFile[$count]->file_id;
            }
            if ($img['TYPE']=='GIF'){
                $file_id = $out->result->animation->file_id;
            }
            if ($img['TYPE']=='MOV'){
                $file_id = $out->result->video->file_id;
            }



            if ($file_id){
                Main::setImageFileID($pic_id, $file_id);
            }else{
                if ($out->ok == false){
                    //$telegram->sendMessage([ 'chat_id' => '153057273', 'text' => $pic_id." vardump \n" . json_encode($out) ]);
                    if ($out->error_code == 400) {
                        if ($repeat != 'mov'){
                            Main::setDeactiveNotOpen($pic_id);
                        }
                        //if (!self::sendV2($endpoint, $type, $telegram, $img, $chat_id, $inlineKeyboardMarkup)) {
                            //повторители
                            if ($repeat) {
                                //$telegram->sendMessage(['chat_id' => '153057273', 'text' => $pic_id . "Не удалось загрузить"]);
                                if ($repeat == 'ngif') {
                                    ngif($telegram, $chat_id);
                                }
                                if ($repeat == 'npic') {
                                    npic($telegram, $chat_id);
                                }
                                if ($repeat == 'gif') {
                                    gif($telegram, $chat_id);
                                }
                                if ($repeat == 'pic') {
                                    pic($telegram, $chat_id);
                                }
                                if ($repeat == 'rdm') {
                                    rdm($telegram, $chat_id);
                                }
                                if ($repeat == 'mov') {
                                    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => "Файл слишком большой, можешь посмотреть по ссылке \n".$img['URL'], 'reply_markup' => json_encode($inlineKeyboardMarkup) ]);
                                    //mov($telegram);
                                }
                            }
                        //}
                    }else{
                        $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $pic_id." some problem"]);
                    }
                }
            }
        }

        return true;
    }



    //если приходит ошибка откыртия файла то блокирует повторители
    /*public static function sendV2($endpoint, $type, $telegram, $img, $chat_id, $inlineKeyboardMarkup, $caption = ''){
        $response = $telegram->uploadFile(
            $endpoint,
            [
                'chat_id' 	=> $chat_id,
                $type	=> ($img['FILE_ID'])?$img['FILE_ID']:$img['URL'],
                'caption'   =>$caption,
                'reply_markup' => json_encode($inlineKeyboardMarkup)
            ]
        );
        if (!$response){
            return false;
        }

        $ans = $response["animation"]['file_id'];

        if ($ans) {
            Main::setActive($pic_id);
            Main::setImageFileID($img['ID'], $ans);
            $telegram->sendMessage(['chat_id' => '153057273', 'text' => $img['ID']." дубль 2 удался" ]);
            return true;
        }else{
            return false;
        }

    }*/

}
