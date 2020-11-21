<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $response = responseGenerator()->success(new UserResource($user));
        return response()->json($response['data'], $response['status']);
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = responseGenerator()->entity($errors->toArray(), true);
            return response()->json($response['data'], $response['status']);
        }

        $data = $validator->validate();
        $user->update($data);
        $response = responseGenerator()->success(new UserResource($user));
        return response()->json($response['data'], $response['status']);
    }
}
