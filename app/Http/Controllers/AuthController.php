<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpMail;
use Tymon\JWTAuth\Token;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordOtp;
use App\Models\commingAfllite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\affiliate_userRequest;
use App\Http\Resources\affiliate_userRsource;
use App\Http\Controllers\Helper\NotficationController;
use App\Models\plan;
use App\Models\UserRanksRelation;
use App\Http\Requests\ImageProfileRequest;
use Illuminate\Support\Carbon;
use App\Models\UsersGenerationRelation;
use App\Http\Controllers\Helper\OTP\OtpRegisterController;
use GuzzleHttp\Client;
use App\Http\Controllers\Affliate\RankControllr;
use App\Models\buffer_user;
use App\Models\Payment;
use App\Models\plan_pakage;
use Illuminate\Support\Facades\Log;





use App\Traits\ResponseJson;



class AuthController extends Controller
{
    
        use ResponseJson;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'create', 'checkOtp', 'me', 'sendOtp', 'reSendOtp', 'resetPassword', 'dymnamikeLink']]);
    }
    
    public function not(Request $request)
    {
        
        $new=new NotficationController();
       return $new->notficationManger("
  نود إعلامك أنه سيتم القيام بأعمال صيانة وتحديثات
  لتنظيم الأمان والجودة على upvela ومن الممكن يترتب على ذلك توقف بعض الخدمات في التطبيق upvela ابتداءً من يوم السبت 7.09.2024 وحتى صباح يوم الاثنين 9/9/2024
  شاكرين تفهمك، فريق upve");
  
  return 55;
        
         // Fetch the user by ID
//      $user = User::find(2365);
//       $fcm=$user->fcm_token;

//      $serverKey = 'AAAAdOBidSQ:APA91bGf83SZcbSaGfybST4Z7y1RHqHV0h1yKgMlB-p09IErYNDo2HXkYiq5aW-iVjgDMQaSinWQNbnJF7vs5m-JPMoILRjoX8kdezLNj54i8gcevawlskPuckqlI9NIxyMzAQKkADWk'; // Replace with your Firebase server key
//     $projectId = 'upvela-9f6e4'; // Replace with your Firebase project ID
//  $projectId = 'your-project-id'; // Replace with your Firebase project ID
 
//     $client = new Client([
//         'base_uri' => 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/',
//         'headers' => [
//             'Authorization' => 'Bearer ' . $serverKey,
//             'Content-Type' => 'application/json',
//         ],
//     ]);

//     $body = "عميلنا العزيز،
// نود إعلامك أنه سيتم القيام بأعمال صيانة وتحديثات
// لتنظيم الأمان والجودة على upvela ومن الممكن يترتب على ذلك توقف بعض الخدمات في التطبيق upvela ابتداءً من يوم السبت 7.09.2024 وحتى صباح يوم الاثنين 9/9/2024
// شاكرين تفهمك، فريق upvela";

//     $message = [
//         'message' => [
//             'token' => $user->fcm_token, // Send notification to the user's FCM token
//             'notification' => [
//                 'title' => 'Upvale Notification',
//                 'body' => $body,
//             ],
//         ],
//     ];

//     try {
//         $response = $client->post('messages:send', ['json' => $message]);
//         $statusCode = $response->getStatusCode();
//         $responseBody = $response->getBody()->getContents();

//         if ($statusCode === 200) {
//             Log::info('Notification sent to user ' . $user->id);
//             return response()->json(['message' => 'Notification sent successfully.']);
//         } else {
//             Log::error('Failed to send notification to user ' . $user->id . ': ' . $responseBody);
//             return response()->json(['error' => 'Failed to send notification.', 'details' => $responseBody], $statusCode);
//         }
//     } catch (\Exception $e) {
//         Log::error('Exception while sending notification to user ' . $user->id . ': ' . $e->getMessage());
//         return response()->json(['error' => 'Exception occurred while sending notification.', 'details' => $e->getMessage()], 500);
//     }
}

 
    


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                'password' => 'required|min:8',
                'phone' => 'required|min:6',

            ]
        );




        if ($validator->fails()) {
            // return json of errors object
            $response = [
                'success' => false,
                "errors" => $validator->errors()
            ];
            return response()->json($response, 200);
        }



      

        if ($request['comming_afflite'] == null) {
            $money = 0;
            $getcomming = commingAfllite::where('status', 1)->first();
            $comming = $request['comming_afflite'] = $getcomming['comming_affliate'];
            $getcomming->subscrib += 1;
            $getcomming->save();
        } else {
            $rules = [
            ];

            $validator = Validator::make(
                $request->all(),
                [

        'comming_afflite' =>  'required|exists:users,affiliate_code|min:5',


                ]
            );

            if ($validator->fails()) {
                // return json of errors object
                $response = [
                    'success' => false,
                    "errors" => $validator->errors()
                ];
                return response()->json($response, 200);
            }


            $comming = $request['comming_afflite'] = $request['comming_afflite'];
            $money = 0;
        }

          $code = $this->generate_affiliate_code();
        //   return 55;
            // return $this->dymnamikeLink($code);
       $userCreated= User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'comming_afflite' => $comming,
            'plan_id' => 1,
            'password' => Hash::make($request['password']),
            'number_points' => 0,
            'affiliate_code'=>$code,
            'affiliate_link'=>'https://upvela.app/'.$code,
            'banned'=>1
            
            
        ]);
        // 
        
 
        
        $this->number_user($comming);
        // send otp to email
        $otp=new OtpRegisterController();
         $otp->otpRegister($request['email']);
        // $this->verifyEmail($request);



        
    Http::post('https://fahd.gfoura.smartidea.tech/api/user-rank-create',[
            'user_id'=>$userCreated->id,
            // 'coming_affiliate'=>$userCreated->comming_affiliate
            ]);
            
   Http::post('https://fahd.gfoura.smartidea.tech/api/father-calculations',[
        'user_id'=>$userCreated->id,
        'coming_affiliate'=>$comming
        ]);
        return $this->login($request);
    }
    public function sendOtp(Request $request)
    {
        // Retrieve the user's email address from the request
        $email = $request->input('email');

        // Check if the email address exists in the database
        $user = User::where('email', $email)->count();
        // check if email is exist

        if ($user == 0) {
            // Email not found
            $response = [
                'success' => false,
                'message' => 'Email is not exist'
            ];
            return response()->json($response);
        }
        $user = User::where('email', $email)->first();


        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Store the OTP in the database for the user
        $user->otp = $otp;
        $user->save();
        $otpDataa = [

            'otp' => $otp,
            'subject' => "reset Password"

        ];
        // Send the OTP to the user's email address
        Mail::to($email)->send(new ResetPasswordOtp($otpDataa)); // Replace with your own mail class

        // Return a success response
        $response = [
            'success' => true,
            'message' => 'OTP sent successfully'
        ];
        return response()->json($response);
    }
    // cheackopt
    public function checkOtp(Request $request)
    {

        try {
            $email = $request->input('email');
            $otp = $request->input('otp');

            $user = User::where('email', $email)->first();
            $userOtp = $user->otp;


            if ($otp == $userOtp) {

                if ($request['action'] != "reset") {
                    $user->otp = null;
                    $user->verified = true;


                    $code = $this->generate_affiliate_code();
                    $user->affiliate_code = $code;
                    $user->email_verified_at = time();
                    // return $code;

                    $user->affiliate_link = $this->dymnamikeLink($code);

                    $user->save();
                    $user2 = User::where('affiliate_code', $user->comming_afflite)->first();
                    $user2->number_of_user = $user2->number_of_user + 1;
                    $user2->save();
                }



                $response = [
                    'success' => true,
                    'message' => 'OTP is valid'
                ];
            } else {
                // OTP is not valid for the given user
                $response = [
                    'success' => false,
                    'message' => 'Invalid OTP'
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function resetPassword(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email address not found'
            ], 200);
        }
        $userOtp = $user->otp;


        if ($otp = $userOtp) {
            if ($request['action'] == "reset") {
                $user->otp = null;
                $user->password = Hash::make($request['password']);
                $user->save();
            }
            $response = [
                'success' => true,
                'message' => 'Password Changed successfully'
            ];
        } else {
            // OTP is not valid for the given user
            $response = [
                'success' => false,
                'message' => 'Invalid OTP'
            ];
        }

        return response()->json($response);
    }
    public function verfiy(Request $request)
    {

        $user = auth('api')->user();

        $otp = $request->otp;
        //  return $otp;
        if ($otp == $user->otp) {
            $user->verified = true;
            $user->otp = null;
            $code = $this->generate_affiliate_code();
            $user->affiliate_code = $code;
            $user->email_verified_at = time();
            // return $code;

            $user->affiliate_link = $this->dymnamikeLink($code);

            $user->save();
            $user2 = User::where('affiliate_code', $user->comming_afflite)->first();
            $user2->number_of_user = $user2->number_of_user + 1;
            $user2->save();

            $response = [
                'success' => true,
                'message' => 'OTP is valid'
            ];
        } else {

            $response = [
                'success' => false,
                'message' => 'Invalid OTP'
            ];
        }
        return response()->json($response);
    }

    function generate_affiliate_code()
    {
        $code = '';
        $chars = array_merge(range('A', 'Z'), range(0, 9));

        // Generate a code with 8 characters
        for ($i = 0; $i < 8; $i++) {
            $code .= $chars[array_rand($chars)];
        }

        // Check if the code already exists in the database
        $existing_users = User::where('affiliate_code', $code)->count();
        if ($existing_users > 0) {
            // If it does, generate a new one recursively
            return $this->generate_affiliate_code();
        }

        return $code;
    }
    public function login(Request $data)
    {
         
        $validator = Validator::make(
            $data->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]
        );

        if ($validator->fails()) {
            // return json of errors object
            $response = [
                'success' => false,
                "errors" => $validator->errors()
            ];
            return response()->json($response, 200);
        }
        $user0 = User::where('email', $data['email'])->first();
        // return $user0;
        if (!$user0) {
            return response()->json([
                'success' => false,
                'message' => 'Email address not Exist'
            ], 200);
        }
        // return $user0;


        $credentials = $data->only(['email', 'password'], 400);
        // return $credentials;
        $token = auth('api')->attempt($credentials);
        // return $token;


        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong Password'
            ], 200);
        }
        $remToken = $user0->remember_token;

        if ($remToken != null) {
            // return $remToken;
            if ($user0->state == "user") {

                JWTAuth::manager()->invalidate(new Token($remToken));
            }
        }

        $user = auth('api')->user();
        // return $user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'invalid token'
            ], 200);
        }
        $user->remember_token = $token;
        $user->save();
        $user->token = $token;


         $user->load(['plan' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->get();

        $createRnke=new RankControllr();
        $createRnke->Create_userRank($user->id,$user->comming_afflite);
         $createRnke->checkRank($user->id);
        
        

        return response()->json([
            'success' => true,

            'user' => $user
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {


        $header = $request->header('Authorization');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'invalid token'
            ], 200);
        }
        if ($user->affiliate_code == null) {
            $code = $this->generate_affiliate_code();
            $user->affiliate_code = $code;
            $user->email_verified_at = time();
            // return $code;

            $user->affiliate_link = $this->dymnamikeLink($code);

            $user->save();
            $user2 = User::where('affiliate_code', $user->comming_afflite)->first();
            $user2->number_of_user = $user2->number_of_user + 1;
            $user2->save();
        }
        $user->load(['plan' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->get();
        $user->token = $header;
        


        if($user->binanceApiKey == 0)
        {
            $user->binanceApiKey=null;
            $user->binanceSecretKey=null;
        }
        
           // to make number_format to user->fess and number point


    
        
        $date1 = Carbon::now();
        $date2 = Carbon::parse($user->end_plan);
        
        $diffInDays = $date1->diffInDays($date2);

         $user->time=$diffInDays;
         
         // for get rank
          $userRank=UserRanksRelation::where('user_id',$user->id)->first();
        //   $user->rank=$userRank->rank_id;
        if ($userRank !== null) {
            $user->rank = $userRank->rank_id;
        } else {
            $user->rank = 1;
        }
          $upline=User::where('affiliate_code',$user->comming_afflite)->first();
          if(!$upline)
          {
                $user->upline="";
          }else{
                $user->upline=$upline->name;
          }
        
          
          // for user money 
        $roundedNumber = number_format($user->number_points, 3, '.', '');
        $roundmoney = number_format($user->money, 3, '.', '');
        $user->number_points=$roundedNumber;
        $user->money=$roundmoney;
       
        
          $payment = Payment::where('user_id', $user->id)->latest()->first();
          $buffer = buffer_user::where('user_id', $user->id)->where('active', 1)->latest()->first();
         if ($payment && $buffer) {
             
             $user->lastPlan=$payment->plan_id;
             $user->per_month=$payment->per_month;
             $planPakage=plan_pakage::select('extraTime')->where('per_month',$payment->per_month)->where('plan_id',$payment->plan_id)->latest()->first();
          $user->extraTime=$planPakage->extraTime;
         }else{
             $user->lastPlan=0;
             $user->per_month=$payment->per_month ?? 0;
             $user->extraTime=0;
         }
        
        return response()->json([
            'success' => true,
            "user" => $user
        ]);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    // public function verifyEmail(Request $data)
    // {
    //     // generate otp from 6 digits
    //     $otp = rand(100000, 999999);
    //     // send it to database
    //     try {
    //         $user = User::where('email', $data['email'])->firstOrFail();
    //         $user->otp = $otp;
    //         $user->save();
    //         // send Mail Otp
    //         $otpDataa = [
    //             'otp' => $otp,
    //             'subject' => "Verify Email"
    //         ];
    //         Mail::to($data['email'])->send(new OtpMail($otpDataa));

    //         return response()->json(['success' => true, 'message' => "OTP sent to email"]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()]);
    //     }
    // }
    // for img profile
     public function uploadImageProfile(ImageProfileRequest $request)
     {
         $user = auth('api')->user();
        // $filename = null;
        
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $extension = $image->getClientOriginalExtension(); // Get the original file extension
        //     $filename = $user->id . '.' . $extension; // Create a filename with timestamp and the original extension
        //     $image->move(public_path('ImageProfile'), $filename);
        // }
        
        // $user->image_profile = $filename;
        // $user->save();

             return response()->json([
                "success" => true,
                "message" => "The image has been uploaded successfully",
            ]);


           

}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function reSendOtp(Request $data)
    {
        // generate otp from 6 digits

        // send it to database
        try {
            $validator = Validator::make(
                $data->all(),
                [
                    'email' => 'required|email:rfc,dns|exists:users,email',
                ]
            );

            if ($validator->fails()) {
                // return json of errors object
                $response = [
                    'success' => false,
                    "errors" => $validator->errors()
                ];
                return response()->json($response, 200);
            }
            $email = $data['email'];
            $user = User::where('email', $email)->firstOrFail();

            // Then, retrieve the OTP from the user's record
            if (!$user) {


                return response()->json(['success' => false, 'message' => "Email not found"]);
            }
            $otp = $user->otp;
            if (!$otp) {
                return $this->sendOtp($data);
            }


            // send Mail Otp
            $otpDataaa = [

                'otp' => $otp,
                'subject' => $data['subject'] ?? 'Verify Email'


            ];
            if ($data['subject'] == "Verify Email") {
                Mail::to($data['email'])->send(new OtpMail($otpDataaa));
            } else {
                Mail::to($data['email'])->send(new ResetPasswordOtp($otpDataaa));
            }





            return response()->json(['success' => true, 'message' => "OTP sent to email"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */



    //  for plus number of user

    public function number_user($affiliate_code)
    {
        $user = User::where('affiliate_code', $affiliate_code)->first();



        $add = $user->number_of_user + 1;
        $user->update(
            [
                'number_of_user' => $add
            ]
        );
        
         $sendNotfiction=new NotficationController();
        $body="مبروك تم اضافة شخص جديد الي فريقك في انتظار المزيد من التقدم";
        $sendNotfiction->notfication($user->fcm_token,$body);
    }
      public function number_userDeleted($affiliate_code)
    {
        $user = User::where('affiliate_code', $affiliate_code)->first();



        $add = $user->number_of_user - 1;
        $user->update(
            [
                'number_of_user' => $add
            ]
        );
        
       
    }


    public function dymnamikeLink($code)
    {

        // $code=$data0['code'];

        $jsonData = [
            'dynamicLinkInfo' => [
                'domainUriPrefix' => 'https://upvela.page.link',
                'link' => 'https://upvela.com/register?code=' . $code,
                'androidInfo' => [
                    'androidPackageName' => 'com.upvela.upvela',
                ],
                'iosInfo' => [
                    'iosBundleId' => 'com.upvela.upvela',
                ],

            ],
        ];




        $response0 = Http::post('https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyAa9-l9PJ2zONYEsqsN84c7JD_9Aue8_pc', $jsonData);

        // return $response0;

        $data = json_decode($response0);
        return $data->shortLink;
    }

    public function deleteUser(Request $request)
    {
        $user = auth('api')->user();
        $user->delete();
        $this->number_userDeleted($user->comming_afflite);
        return response()->json(['success' => true, 'message' => "User Deleted"]);
    }

    public function affiliate_user(Request $request)
    {   
        
        
            $userToken=auth('api')->user(); // this user open app
 
        // if request have user_id
        if($request['user_id'])
        {
         $user = User::where('id', $request['user_id'])->first(); // click in  user 
         $affiliate_code = $user->affiliate_code;

         $users = User::with(['plan', 'historyAllProfit' => function ($query) use ($user) {
        $query->where('markting_id', $user->id);
        }])->where('comming_afflite', $affiliate_code)->get();
        
         // for get rank
       



   
        $plan = Plan::select('percentage1','percentage2', 'percentage3')->find(6);
        
        // for loop users
         $users->each(function ($user) use ($plan,$userToken)  {
        $totlefess= $user->historyAllProfit->where('Generations', 1)->sum('profit_users');
        $user->totleMony=number_format($user->historyAllProfit->sum('amount'), 4);
        
        // check this user active or not
        $userGenration=UsersGenerationRelation::where('user_id_child',$user->id)->where('user_id_father',$userToken->id)->first();
        // $user_free_check=$userGenration->free_check;
        
        if($userGenration->free_check == 1)
        {
            $user->Active=1; // this user is not active
        }else{
            $user->Active=0;
        }
        // end user rnak active
           $userRank=UserRanksRelation::where('user_id',$user->id)->first();
           $user->rank=$userRank->rank_id;
    
        $g1 = +$plan->percentage1 / 100 * $totlefess;
        $g2 = +$plan->percentage2 / 100 * $totlefess;
        $g3 = +$plan->percentage3 / 100 * $totlefess;

        $user->G1 = number_format($g1,4);
        $user->G2 =number_format($g2,4);
        $user->G3 = number_format($g3,4);
        });
        
        // for get rank
          $userRank=UserRanksRelation::where('user_id',$user->id)->first();
          $user->rank=$userRank->rank_id;
    
        // Return the modified collection
        return response()->json([
            "success" => true,
            "status"=>200,
            "data" => $users,
            "statusCode"=>200,
        ]);
            
        }else{
            // for number genration
              $generationNumber=$request['generation_number'];
           
              
             
          
          $getAllChildGenerationIds = UsersGenerationRelation::where('user_id_father', $userToken->id)->where('generation_id', $generationNumber)->pluck('user_id_child')->toArray();
        $users = User::with(['plan', 'historyAllProfit'])->whereIn('id', $getAllChildGenerationIds)->get();
         $plan = Plan::select('percentage1','percentage2', 'percentage3')->find(6);
         $users->each(function ($user) use ($plan,$userToken)  {
         $totlefess= $user->historyAllProfit->where('Generations', 1)->sum('profit_users');


    //   = $user->historyAllProfit->sum('amount');
        $user->totleMony=number_format($user->historyAllProfit->sum('amount'), 4);
           // check this user active or not
        $userGenration=UsersGenerationRelation::where('user_id_child',$user->id)->where('user_id_father',$userToken->id)->first();
        $user_free_check=$userGenration->free_check;
        
        if($user_free_check == 0)
        {
            $user->Active=0; // this user is not active
        }else{
            $user->Active=1;
        }
        // end user rnak active
         // for get rank
                  $userRank=UserRanksRelation::where('user_id',$user->id)->first();
                  $user->rank=$userRank->rank_id;
    
        $g1 = +$plan->percentage1 / 100 * $totlefess;
        $g2 = +$plan->percentage2 / 100 * $totlefess;
        $g3 = +$plan->percentage3 / 100 * $totlefess;

        $user->G1 = number_format($g1,4);
        $user->G2 =number_format($g2,4);
        $user->G3 = number_format($g3,4);
        });
    
        // Return the modified collection
        return response()->json([
            "success" => true,
            "data" => $users
        ]);
        }
        
        
        
        
        
        
        
        
        
        
    }

    public function softDeleteUser()
    {
        $deletedUsers = User::onlyTrashed()
            ->where('deleted_at', '!=', null)
            ->get();

        return response()->json(['success' => true, 'message' => "User Deleted", 'data' => $deletedUsers]);
    }

    public function restoreSoftDeleteUser($id)
    {

        $user = User::onlyTrashed()->find($id);
        if ($user) {
            $user->restore();
            $user->deleted_at = null;
            $user->save();
        }

        return response()->json(['success' => true, 'message' => "User Restored"]);
    }

    // for fcm token
    public function fcmToken(Request $request)
    {

        $user = auth('api')->user();  // Get the authenticated user using the 'api' guard
        $user['fcm_token'] = $request['fcm'];
        $user->save();


        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'FCM token updated successfully'
        ]);
    }
}
