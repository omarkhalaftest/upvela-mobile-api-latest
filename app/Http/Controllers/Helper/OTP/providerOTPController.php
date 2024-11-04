<?php

namespace App\Http\Controllers\Helper\OTP;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Exception\ClientException;



class providerOTPController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            // You can add any Guzzle options here if needed
        ]);
    }

    public function sendOtp($email, $name, $otpData)
    {
        // return $name;

        try {

            $htmlContent = View::make('otpmail', ['name' => $name, 'otpData' => $otpData])->render();
            $response = $this->client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => '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',
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name' => 'Upvela.com',
                        'email' => 'upvela9@gmail.com'
                    ],
                    'to' => [
                        [
                            'email' => $email,
                            'name' => 'Recipient'
                        ]
                    ],
                    'subject' => "Upvela",
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
}
