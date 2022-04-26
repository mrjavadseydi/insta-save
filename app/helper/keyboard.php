<?php

use App\Models\Plan as Plan;
use Telegram\Bot\Keyboard\Keyboard;

function mainMenu()
{

    $home = [
        [
            '🤔 راهنما',
            '👤 حساب کاربری من'

        ],
        [
            '🚸 معرفی به دوستان (سکه رایگان)',
        ],
    ];

    return Keyboard::make(['keyboard' => $home, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

}
function adminMenu()
{

    $home = [
        [
            'آمار',

        ],
        [
            'پیام همگانی',
        ],
        ['بازگشت ↪️']
    ];

    return Keyboard::make(['keyboard' => $home, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

}
function sendAllMenu()
{

    $home = [
        [
            'انصراف',
        ],
        [
            'ارسال',

        ],
        [
            'انصراف',
        ],
    ];

    return Keyboard::make(['keyboard' => $home, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

}

if (!function_exists('backButton')) {
    function backButton()
    {

        $btn = Keyboard::button(
            [
                ['بازگشت ↪️']
            ]
        );
        return Keyboard::make(['keyboard' => $btn, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
    }
}
if (!function_exists('shotTypeButton')) {
    function shotTypeButton($analize = false)
    {
        if ($analize) {
            $row = [
                'شات داف',
                'شات عمومی',
                'شات انالیز',
            ];
        } else {
            $row = [
                'شات داف',
                'شات عمومی',
            ];
        }
        $btn = Keyboard::button(
            [
                $row,
                ['بازگشت ↪️']
            ]
        );
        return Keyboard::make(['keyboard' => $btn, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
    }
}
if (!function_exists('shotBackButton')) {
    function shotBackButton()
    {
        $btn = Keyboard::button(
            [
                ['اتمام و ارسال'],
                ['حذف شات و بازگشت']
            ]
        );
        return Keyboard::make(['keyboard' => $btn, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
    }
}
if (!function_exists('plans_key')) {
    function plans_key()
    {
        $plans = Plan::orderBy('request_count', 'asc')->get();
        $arr = [];
        foreach ($plans as $plan) {
            $arr[] =[ [
                'text' => str(' ')
                    ->append($plan->request_count)
                    ->append(" درخواست به قیمت")
                    ->append(number_format($plan->price))
                    ->append("ریال")
                    ->toString(),
                'callback_data' => "vip-{$plan->id}"
            ]]

            ;
        }
        return keyboard::make([
            'inline_keyboard' =>
                $arr
            ,
        ]);
    }
}
//if (!function_exists('join_key')) {
//    function join_key($link)
//    {
//        $plans = Plan::orderBy('day','asc')->get();
//        $arr = [
//            [
//                'text'=>"عضویت در کانال",
//                'url'=>$link
//            ],
//            [
//                'text'=>'عضو شدم',
//                'url'=>""
//            ]
//        ];
//        return keyboard::make([
//            'inline_keyboard' => [
//                $arr
//            ],
//        ]);
//    }
//}
