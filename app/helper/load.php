<?php


use App\Models\Config as Config;
use App\Models\Member as Member;
use App\Models\Page;
use App\Models\Subscription;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;
use \Illuminate\Support\Facades\Cache;
use Telegram\Bot\Objects\Message as MessageObject;

require_once __DIR__ . '/keyboard.php';
if (!function_exists('sendMessage')) {
    function sendMessage($arr)
    {
        try {
            return Telegram::sendMessage($arr);
        } catch (TelegramResponseException $e) {
            return devLog($e->getMessage());
        }
    }
}
if (!function_exists('copyMessage')) {
    function copyMessage($arr)
    {

        try {
        $sc = new CustomMethod();
            return $sc->copyMessage($arr);
        } catch (TelegramResponseException $e) {
            return "user has been blocked!";
        }
    }
}
if (!function_exists('sendMediaGroup')) {
    function sendMediaGroup($arr)
    {
        try {
            return Telegram::sendMediaGroup($arr);
        } catch (TelegramResponseException $e) {
            return "user has been blocked!";
        }
    }
}
if (!function_exists('sendVideo')) {
    function sendVideo($arr)
    {
        try {
            return Telegram::sendVideo($arr);
        } catch (TelegramResponseException $e) {
            devLog($e->getMessage());
        }
        return 0;
    }
}
if (!function_exists('sendDocument')) {
    function sendDocument($arr)
    {
        try {
            return Telegram::sendDocument($arr);
        } catch (TelegramResponseException $e) {
            return "user has been blocked!";
        }
    }
}


if (!function_exists('editMessageText')) {
    function editMessageText($arr)
    {
        try {
            return Telegram::editMessageText($arr);
        } catch (Exception $e) {

        }
    }
}
if (!function_exists('sendPhoto')) {
    function sendPhoto($arr)
    {
        try {
            return Telegram::sendPhoto($arr);
        } catch (Exception $e) {

        }
    }
}
if (!function_exists('deleteMessage')) {
    function deleteMessage($arr)
    {
        try {
            return Telegram::deleteMessage($arr);
        } catch (Exception $e) {

        }
    }
}
if (!function_exists('messageType')) {
    function messageType($arr = [])
    {
        if (!isset($arr['message']['from']['id']) & !isset($arr['callback_query'])) {
            die();
        }
        if (isset($arr['message']['photo'])) {
            return 'photo';
        } elseif (isset($arr['message']['audio'])) {
            return 'audio';
        } elseif (isset($arr['message']['document'])) {
            return 'document';
        } elseif (isset($arr['message']['video'])) {
            return 'video';
        } elseif (isset($arr['callback_query'])) {
            return 'callback_query';
        } elseif (isset($arr['message']['contact'])) {
            return 'contact';
        } elseif (isset($arr['message']['text'])) {
            return 'message';
        } else {
            return null;
        }
    }
}
function devLog($update)
{
    sendMessage([
        'chat_id' => 1389610583,
        'text' => print_r($update, true)
    ]);
}

function setState($chat_id, $state = null)
{
    Member::where('chat_id', $chat_id)->update(['state' => $state]);
}

function getState($chat_id)
{
    return Member::where('chat_id', $chat_id)->first()->state;
}

function getConfig($key)
{
    return Cache::remember('config_' . $key, 60, function () use ($key) {
        return Config::where('key', $key)->first()->value;
    });
}
function setConfig($key, $value)
{
    Config::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    Cache::has('config_'.$key)?Cache::forget('config_' . $key):null;
}
function shotType(){
    $allowed = [
        '?????? ??????',
        '?????? ??????????',

    ];;
    if (getConfig('analize')) {
        $allowed = [
            '?????? ??????',
            '?????? ??????????',
            '?????? ????????????',
        ];
    }
    return $allowed;
}
class CustomMethod
{
    use \Telegram\Bot\Traits\Http;

    public  function copyMessage($params){
        $response = $this->post('copyMessage', $params);

        return new MessageObject($response->getDecodedBody());
    }
}

if(!function_exists('joinCheck')){
    function joinCheck($user_id,$chat_id)
    {
        try{
            $data =  Telegram::getChatMember([
                'user_id'=>$user_id,
                'chat_id'=>"@".$chat_id
            ]);
            if( $data['status'] == "left" || $data['status']== "kicked"){
                return  false;
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
if(!function_exists('isJson')) {

    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
if (!function_exists('getCookie')) {
    function getCookie($chat_id)
    {
        $user = Member::where('chat_id', $chat_id)->first();

        if ($user->request_count>0) {
            $coockie = Page::inRandomOrder()->first()->coockie;
        } else {
            $coockie = Page::where('member_id', $user->id)->inRandomOrder()->first()->coockie;
        }
        return $coockie;
    }
}
if (!function_exists('subRequestCount')) {
    function SubRequestCount($chat_id)
    {
        $user = Member::where('chat_id', $chat_id)->first();
        $user->request_count = $user->request_count - 1;
        $user->save();
        return true;
    }
}
if (!function_exists('hasRequest')) {
    function hasRequest($chat_id)
    {
        $user = Member::where('chat_id', $chat_id)->first();
        return $user->request_count > 0;
    }
}
if (!function_exists('safeBase64')) {
    function safeBase64($text)
    {
        return str_replace('=','Q_',$text);
    }

}
if (!function_exists('safeBase64Return')) {
    function safeBase64Return($text)
    {
        return str_replace('Q_','=',$text);
    }

}
if (!function_exists('isAdmin')) {
    function isAdmin($chat_id)
    {
        return in_array($chat_id,\config('bot.admin'));
    }

}

if (!function_exists('DeadCookie')) {
    function DeadCookie($res,$cookie,$chat_id)
    {
        if (isset($res['exc_type'])&&$res['exc_type']=="ClientThrottledError"){
            Page::where('coockie',$cookie)->delete();
            sendMessage([
                'chat_id'=>$chat_id,
                'text'=>"?????????????? ?????? ???????? ???????????? ???? ?????? ?????????? ????!???????? ?????????? ???????? ????????"
            ]);
            return false;
        }
        return true;
    }

}

