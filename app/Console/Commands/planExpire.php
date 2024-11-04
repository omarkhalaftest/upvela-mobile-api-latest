<?php

namespace App\Console\Commands;

use App\Models\video;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use App\Models\bots_usdt;
use App\Http\Controllers\Helper\BotTelgremController;
use App\Http\Controllers\Helper\NotficationController;




class planExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'planExpire every all mounth';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         
        $telgrem=new BotTelgremController(); // for telgrem
        $notfication = new NotficationController();


          $date = Carbon::now()->format('Y-m-d');

    // Get users whose 'end_plan' is less than or equal to the current date
      $users = User::whereDate('end_plan', '<=', $date)->get();

    // Update bot statuses for each user
    foreach ($users as $user) {
        // Update 'bot_status' to 0 for all associated bots
        $user->bots_usdt()->update(['bot_status' => 0]);

        // Update user information
        $user->update([
    
            'end_plan' => null,
            'plan_id' => 1,
            'Status_Plan' => null,
            'is_bot' => 0,
        ]);
        
        
        $bodyManager = "from function expire plan .$user->name .$user->email";
        $telgrem->upvaleFreeGroupe($bodyManager);
        $textforexpireplan="تم انتهاء الباقه الشهريه لديك يرجي تجديد الباقه ";
        $notfication->notfication($user->fcm_token,$textforexpireplan);
    }
    
 
  
    
    
        
        
        
            }
}
