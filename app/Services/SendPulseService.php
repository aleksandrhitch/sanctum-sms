<?php


namespace App\Services;

use App\Models\Client;
use App\Models\Sms;
use Faker\Factory;
use http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class SendPulseService
{
    /**
     * @var ApiClient
     */
    private $client;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $id;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private $secret;

    /**
     * SendPulseService constructor.
     * @param $id
     * @param $secret
     */
    public function __construct()
    {
        $this->id = config('sendpulse.id');
        $this->secret = config('sendpulse.secret');
        $this->client = new ApiClient(config('sendpulse.id'), config('sendpulse.secret'), new FileStorage());
    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sendSms($user)
    {
        $smsCode = $this->generateSmsCodeForLogin();
        $bookId = $this->addAddressBook();

        $params = [
            'sender' => 'Author',
            'body' => 'SMS code to enter the site: '.$smsCode
        ];

        $additionalParams = [
            'transliterate' => 1
        ];


        if ($this->addPhoneNumber($bookId, $user->phone) == true) {
            $response = $this
                ->client
                ->sendSmsByBook($bookId, $params, $additionalParams);

            $smsEntity = new Sms(['code' => $smsCode]);
            $user->sms()
                ->save($smsEntity);

            if ($response->result == true) {
                return response()->json([
                        'status' => 'success',
                        'message' => 'The code was successfully sent to the specified number'
                ]);
            }

            if ($response->result == false) {
                return response()->json([
                        'status' => 'error',
                        'message' => 'Insufficient funds in the account'
                ], 400);
            }
        }

        return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while submitting the code'
        ], 400);
    }

    /**
     * Get token
     * @return null
     */
    public function getToken()
    {
        $request = Http::post('https://api.sendpulse.com/oauth/access_token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->id,
            'client_secret' => $this->secret
        ]);
        $response = json_decode("".$request->body()."");
        return $response ? $response->access_token : null;
    }

    /**
     * add phone number
     * @throws \Exception
     */
    public function addPhoneNumber($bookId, $phone)
    {
        $data = [
            $phone => [
                [
                    [
                        'name' => 'name',
                        'type' => 'string',
                        'value' => 'variable value',
                    ]
                ]
            ]
        ];
        $response = $this->client->addPhonesWithVariables($bookId, $data);
        return $response->result;
    }

    /**
     * add book
     */
    public function addAddressBook()
    {
        $token = $this->getToken();
        $request = Http::withToken($token)->post('https://api.sendpulse.com/addressbooks', [
            'bookName' => Str::random(10)
        ]);

        $result = json_decode($request->body());
        return $result->id;
    }

    /**
     * generate unique code
     * @return int
     */
    public function generateSmsCodeForLogin()
    {
        $faker = Factory::create();
        return $faker->unique()->randomNumber(5);
    }

}
