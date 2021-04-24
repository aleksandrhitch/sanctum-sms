<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthClientLoginRequest;
use App\Http\Requests\AuthClientSmsRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\SendPulseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthClientController extends Controller
{
    /**
     * @var SendPulseService
     */
    private $sendPulseService;

    /**
     * AuthClientController constructor.
     * @param $sendPulseService
     */
    public function __construct(SendPulseService $sendPulseService)
    {
        $this->sendPulseService = $sendPulseService;
    }

    /**
     * Send sms with code for login
     * @param AuthClientLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function sendSmsCode(AuthClientSmsRequest $request)
    {
        $phone = $request->validated()['phone'];
        $user = Client::where('phone', $phone)
            ->first();

        if (! $user) {
            return response()->json([
                'error' => 'You entered the wrong phone number'
            ], 401);
        }

        return $this->sendPulseService->sendSms($user);
    }

    /**
     * Login client with code
     * @param AuthClientLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthClientLoginRequest $request)
    {

        $user = Client::where('phone', $request->validated()['phone'])
            ->first();

        if(! $user) {
            return response()->json([
                'error' => 'You entered the wrong phone number'
            ], 401);
        }

        $generatedCode = $user->sms->last()->code;
        if ($generatedCode != $request->validated()['code']) {
            return response()->json([
                'error' => 'You entered an invalid code'
            ], 401);
        }

        return response()
            ->json([
                'token' => $user->createToken(Str::random(10))->plainTextToken
            ], 200);
    }

    public function getClient(Request $request)
    {
        return $request->user();
    }
}
