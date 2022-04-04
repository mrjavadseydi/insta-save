<?php

use App\Models\Plan as Plan;
use Telegram\Bot\Keyboard\Keyboard;

function mainMenu($isVip=false)
{

    $home = [
        [
            '📮 دریافت بنر 📮'
        ],
        [
            '✔️ ثبت دان ✔️',
            '🖼 ارسال شات 🖼'
        ],

        ['📨 پشتیبانی 📨',
            '💰 کیف پول💰']
        ,

        ['🤔 راهنما 🤔',
            "🔱VIP🔱",
            '💲قیمت امروز💲'
        ]

    ];
    $vip_home = [
        [
            '📮 دریافت بنر 📮'
        ],

        [    '🔱 ثبت دان 🔱',
            '🔱 ارسال شات 🔱'
        ],
        [    '📨 پشتیبانی 📨',
            '💰 کیف پول💰'
        ],
        [    '📃 راهنما 📃',
            '💲قیمت امروز💲'
        ]
    ];
    if($isVip){
        $key = Keyboard::button($vip_home);
    }else{
        $key = Keyboard::button($home);
    }
    return Keyboard::make(['keyboard' => $key, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

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
    function shotTypeButton($analize=false)
    {
        if ($analize){
            $row = [
                'شات داف',
                'شات عمومی',
                'شات انالیز',
            ];
        }else{
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
        $plans = Plan::orderBy('day','asc')->get();
        $arr = [];
        foreach ($plans as $plan){
            $arr[] = [[
                'text' => str('اشتراک ')
                    ->append($plan->day)
                    ->append("به قیمت")
                    ->append(number_format($plan->price))
                    ->append("ریال")
                ->toString()

                ,
                'callback_data' => "vip-{$plan->id}"
            ]];
        }
        return keyboard::make([
            'inline_keyboard' => [
                $arr
            ],
        ]);
    }
}
