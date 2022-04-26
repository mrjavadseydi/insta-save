<?php

namespace App\Lib\Classes\Admin;

use App\Lib\Interfaces\TelegramOprator;
use App\Models\Invite;
use App\Models\Member;
use App\Models\Page;
use App\Models\Report;
use Telegram\Bot\FileUpload\InputFile;

class Status extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type == "message" && isAdmin($this->chat_id) && ($this->text == "آمار")&&getState($this->chat_id)=="admin_menu");
    }

    public function handel()
    {
        $users = Member::query()->count();
        $pages = Page::query()->count();
        $week_users = Member::query()->where('created_at','>',now()->subWeek())->count();
        $week_pages = Page::query()->where('created_at','>',now()->subWeek())->count();
        $today_post = Report::query()->where([['action','post'],['created_at','>',now()->startOfDay()]])->count();
        $today_profile = Report::query()->where([['action','profile'],['created_at','>',now()->startOfDay()]])->count();
        $today_igtv = Report::query()->where([['action','igtv'],['created_at','>',now()->startOfDay()]])->count();
        $today_reel = Report::query()->where([['action','reel'],['created_at','>',now()->startOfDay()]])->count();
        $today_story = Report::query()->where([['action','story'],['created_at','>',now()->startOfDay()]])->count();
        $sum_request = $today_igtv+$today_post+$today_profile+$today_reel+$today_story;
        $text = str('آمار ربات : ')->append("\n")
            ->append("تعداد کل کاربران :  $users")->append("\n")
            ->append("تعداد  کاربران این هفته :  $week_users")->append("\n")
            ->append("تعداد  کل پیج ها :  $pages")->append("\n")
            ->append("تعداد  پیج های این هفته :  $week_pages")->append("\n")
            ->append("تعداد کل درخواست های امروز : $sum_request")->append("\n")
            ->append("تعداد درخواست های دانلود پست امروز : $today_post")->append("\n")
            ->append("تعداد درخواست های دانلود پروفایل امروز : $today_profile")->append("\n")
            ->append("تعداد درخواست های دانلود ای جی تی وی امروز : $today_igtv")->append("\n")
            ->append("تعداد درخواست های دانلود ریل امروز : $today_reel")->append("\n")
            ->append("تعداد درخواست های دانلود استوری امروز : $today_story")->append("\n")
            ->toString();
        sendMessage([
            'chat_id'=>$this->chat_id,
            'text'=>$text,
        ]);
    }
}
