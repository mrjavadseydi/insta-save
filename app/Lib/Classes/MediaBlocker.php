<?php

namespace App\Lib\Classes;

use App\Lib\Interfaces\TelegramOprator;

class MediaBlocker extends TelegramOprator
{

    public function initCheck()
    {
        return ($this->message_type!="message");
    }

    public function handel()
    {
    }

}
