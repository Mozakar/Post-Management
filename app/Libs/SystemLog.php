<?php
namespace App\Libs;

use Exception;
use Illuminate\Support\Facades\Log;

class SystemLog {



    public function LoginLog()
    {
        return array('type' => 'login');
    }

    public function StatusLog($action)
    {
        return ['type' => 'changeStatus', 'action' => $action];
    }


    public function insert($data, $user_id = 0)
    {
        try {
            $ip = request()->ip();
            if ($user_id == 0) {
                $user_id = auth()->user()->id;
            }
            $log = new \App\Models\SystemLog();
            $log->user_id = $user_id;
            $log->ip = $ip;
            $log->data = $data;
            $log->save();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }


    /**
     * User Auth Verification Code
     */
    public function generateVerificationCode()
    {
        $code = rand(1000, 9999);
        return ['type' => 'verificationCode', 'data' => $code];
    }

    public function getUserVerificationCode($user_id)
    {
        return \App\Models\SystemLog::where('user_id', $user_id)
            ->where('data->type', 'verificationCode')
            ->orderBy('created_at', 'desc')
            ->first();

    }

    public function getUserVerificationCodeWithIp()
    {
        $ip = request()->ip();
        return \App\Models\SystemLog::where('ip', $ip)
                    ->where('data->type', 'verificationCode')
                    ->orderBy('created_at', 'desc')
                    ->first();

    }
}
?>
