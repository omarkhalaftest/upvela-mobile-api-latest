<?php

namespace App\Http\Controllers\V2\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\commingAfllite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\Login\RegisterRequest;
use App\Http\Requests\Profile\ProfileRequest;
use App\Http\Controllers\Helper\NotficationController;
use App\Traits\ResponseJson;

class AuthsController extends Controller
{
    use ResponseJson;
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            // You can add any Guzzle options here if needed
        ]);
    }
    public function createTest(RegisterRequest $request)
    {
        if ($request['comming_afflite'] == null) {
            $request['comming_afflite'] = $this->commingAfllite();
        }


        $code = $this->generate_affiliate_code();



        $userCreated = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'comming_afflite' => $request['comming_afflite'],
            'plan_id' => 1,
            'password' => Hash::make($request['password']),
            'number_points' => 0,
            'affiliate_code' => $code,
            'affiliate_link' => $this->dymnamikeLink($code),
            'banned' => 1

        ]);


        $this->number_user($request['comming_afflite']);
        $this->CreateRank($$userCreated->id, $comming_afflite);
        // send otp to email
        // $otp = new OtpRegisterController();
        // $otp->otpRegister($request['email']);
        // $this->verifyEmail($request);

    }

    public function commingAfllite()
    {

        $getcomming = commingAfllite::where('status', 1)->first();
        // $comming = $request['comming_afflite'] = ;
        $getcomming->subscrib += 1;
        $getcomming->save();

        return $getcomming['comming_affliate'];
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

    public function number_user($affiliate_code)
    {
        $user = User::where('affiliate_code', $affiliate_code)->first();



        $add = $user->number_of_user + 1;
        $user->update(
            [
                'number_of_user' => $add
            ]
        );

        $sendNotfiction = new NotficationController();
        $body = "مبروك تم اضافة شخص جديد الي فريقك في انتظار المزيد من التقدم";
        $sendNotfiction->notfication($user->fcm_token, $body);
    }


    public function sendOtp($email,)
    {
        $to = "ahmedsamir11711@gmail.com";
        try {

            $htmlContent = View::make('otpmail', ['name' => "Ahmed", 'otpData' => 12365])->render();
            $response = $this->client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => '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',
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name' => 'Upvela come',
                        'email' => 'ahmedsamir11711@gmail.com'
                    ],
                    'to' => [
                        [
                            'email' => $to,
                            'name' => 'Recipient'
                        ]
                    ],
                    'subject' => "test",
                    "htmlContent" => $htmlContent,
                ],
            ]);

            return $response->getBody();
        } catch (ClientException $e) {
            // Handle client errors (4xx)
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = $e->getResponse()->getBody()->getContents();
            return "Client Error $statusCode: $errorMessage";
        } catch (\Exception $e) {
            // Handle other errors
            return "Error: " . $e->getMessage();
        }
    }

    public function CreateRank($user_id, $comming_afflite)
    {
        Http::post('https://upvela.gfoura.smartidea.tech/api/user-rank-create', [
            'user_id' => $user_id,
            // 'coming_affiliate'=>$userCreated->comming_affiliate
        ]);

        Http::post('https://upvela.gfoura.smartidea.tech/api/father-calculations', [
            'user_id' => $user_id,
            'coming_affiliate' => $comming_afflite,
        ]);
    }

    public function updateProfile(ProfileRequest $request)
    {
          $user = auth('api')->user();

        $user->update($request->only(['name', 'email'])); // Add other fields as needed
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return $this->success('Profile updated successfully');
        // return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
}
