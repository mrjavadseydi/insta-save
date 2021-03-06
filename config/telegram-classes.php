<?php
return[
    'classes'=>[
        1=>[
            \App\Lib\Classes\MediaBlocker::class,
            \App\Lib\Classes\JoinCheck::class,
            \App\Lib\Classes\Start::class,
            \App\Lib\Classes\Help::class,
            \App\Lib\Classes\Banner::class,
            \App\Lib\Classes\Profile::class,
            \App\Lib\Classes\HowAdd::class,
            \App\Lib\Classes\HowCoin::class,
            \App\Lib\Classes\HowCopy::class,
            \App\Lib\Classes\HowDownload::class,
            \App\Lib\Classes\HowInvite::class,
        ],
        2=>[
            \App\Lib\Classes\LoginAccount::class,
            \App\Lib\Classes\BuyVipCallBack::class
        ],
        3=>[
            \App\Lib\Classes\Admin\SendAll::class,
            \App\Lib\Classes\Admin\SendAllCheck::class,
            \App\Lib\Classes\Admin\SendAllAction::class,
            \App\Lib\Classes\AddAcount::class,
            \App\Lib\Classes\BuyVip::class,
            \App\Lib\Classes\Admin\Menu::class,
            \App\Lib\Classes\Admin\Status::class,
            \App\Lib\Classes\GetLink::class,
        ]
    ]
];
