<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ReloginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relogin:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        foreach (Page::all() as $page){
            $username = $page->username;
            $password = $page->password;
            $request = Http::timeout(130)->asForm()->post('http://194.5.192.39:8000/auth/login', [
                'username' => $username,
                'password' => $password,
            ]);
            $body = $request->body();
            print_r($body);

                $page->update([
                    'coockie'=> str_replace('"', '', $body)
                ]);
                print ("page {$username} updated! \n");

            sleep(5);
        }
        return 0;
    }
}
