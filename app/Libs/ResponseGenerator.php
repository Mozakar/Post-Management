<?php

namespace App\Libs;
class ResponseGenerator {

    /**
     * Success
     */
    public static function success($data = []){
        $status = ['status' => 'success'];
        $data = ['body' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 200];
    }

    //Unauthorized
    public static function unauthorized($data = []){
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 401];
    }

    //Forbidden
    public static function forbidden($data = []){
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 403];
    }
    //Not found
    public static function notFound($data = []){
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 404];
    }

    //Method not allowed
    public static function notAllowed($data = []){
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 405];
    }

    //Duplicate entry
    public static function conflict($data = []){
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 409];
    }

    //Unprocessable entity
    public static function entity($data = [], $format = false){
        if($format) $data = self::format($data);
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 422];
    }

    public static function unprocessableEntity($data = [], $format = false){
        if($format) $data = self::format($data);
        $status = ['status' => 'error'];
        $data = isset($data['errors']) ? $data : ['errors' => $data];
        $data = $status + $data;
        return ['data' => $data , 'status' => 422];
    }

    //Server error
    public static function serverError($data = []){
        $status = ['status' => 'error'];
        $data = $status + $data;
        return ['data' => $data , 'status' => 500];
    }


    public static function format($errors){
        $response = [];
        foreach($errors as $key => $value){
            $response[$key] = $value[0];
        }
        return ['errors' => $response];
    }
}
