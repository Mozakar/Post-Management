<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Rules\CheckEmailMobile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Api\V1\Auth\IAuthService;

class AuthController extends Controller
{
    private $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }


    public function loginOrRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_mobile'   => [
                'required',
                new CheckEmailMobile
            ],
            'password'  => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }
        $response = $this->authService->loginOrRegister($request);
        return response()->json($response['data'], $response['status']);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_mobile'   => [
                'required',
                new CheckEmailMobile
            ],
            'verification_code'  => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->unprocessableEntity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }

        $response = $this->authService->verify($request);
        return response()->json($response['data'], $response['status']);
    }


    public function logout(Request $request)
    {
        $response = $this->authService->logout($request);
        return response()->json($response['data'], $response['status']);
    }
}
