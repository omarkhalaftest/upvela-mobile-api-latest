<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\bots_usdt;
use Illuminate\Console\Command;
use App\Http\Controllers\Helper\NotficationController;


class cheakMony extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheakMony';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cheakMony for stop bots';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

// return 1234;
$notfiction=new NotficationController();


// Get users whose 'end_plan' is less than or equal to the current date
    //   $users = User::where('number_points', '<=', 0)->get();
      $users = User::where('id',  8)->get();

// Update bot statuses for each user
foreach ($users as $user) {
    // Update 'bot_status' to 0 for all associated bots
    $user->bots_usdt()->update(['bot_status' => 0]);

    // Update user information
    $user->update([
        'is_bot' => 0,
    ]);

    $text="رصيد الفيز لديك اقل من 1$ يرجي شحن الفيز لتشغبل الابوات مره اخري ";
    $notfiction->notfiction($user->fcm_token,$text);


}



        }

    }
