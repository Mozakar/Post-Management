<?php
namespace App\Services\Api\V1\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Jobs\SendVerificationCodeBySms;
use App\Jobs\SendVerificationCodeByEmail;

class AuthService implements IAuthService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * login Or Register function
     *
     * @param Request $request
     * @return json
     */
    public  function loginOrRegister($request){

        $emailOrMobile = helper()->enNum($request['email_or_mobile']);
        $emailOrMobile = str_replace(['(', ')', '+'], ['', '', ''], $emailOrMobile);
        // check login field
        $login_type = filter_var($emailOrMobile, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';


        //search by mobile and find user if exist
        $user = $this->user
                     ->where('mobile', $emailOrMobile)
                     ->orWhere('email', $emailOrMobile)
                     ->first();


        if($user && $request->has('password')){
            return $this->loginWithPassword($emailOrMobile, $request);
        }

        $is_new_user = false;
        if (empty($user)) {
            $user = $this->register($emailOrMobile, $login_type);
            $is_new_user = true;
        }

        $otp_resend_wait_time = config('blog.otp_resend_wait_time', 180);
        $lastVerificationCode = logActivity()->getUserVerificationCode($user->id);
        $lastVerificationCodeWithIp = logActivity()->getUserVerificationCodeWithIp();
        $lastVerificationCodeWithIpDiff = $lastVerificationCodeWithIp ?  Carbon::now()->diffInSeconds(new Carbon($lastVerificationCodeWithIp->created_at)) : $otp_resend_wait_time + 5;
        if( ($lastVerificationCode &&
            Carbon::now()->diffInSeconds(new Carbon($lastVerificationCode->created_at)) < $otp_resend_wait_time)
            || ($lastVerificationCodeWithIpDiff < $otp_resend_wait_time)){

            $now_sec = $lastVerificationCode ? Carbon::now()->diffInSeconds(new Carbon($lastVerificationCode->created_at))  :  $lastVerificationCodeWithIpDiff;
            $wait_time_seconds = $otp_resend_wait_time - $now_sec;

            return responseGenerator()->forbidden([
                'errors' => [
                    'resend_wait_time' => $otp_resend_wait_time,
                    'wait_time_seconds' => $wait_time_seconds <= 0 ? 0 : $wait_time_seconds
                ]
            ]);

        }

        $verificationCode = logActivity()->generateVerificationCode();
        logActivity()->insert($verificationCode, $user->id);

        if ($login_type == 'email') {
            SendVerificationCodeByEmail::dispatch($user);
        } else {
            SendVerificationCodeBySms::dispatch($user);
        }

        $response =  [
            'login_type' => $login_type,
            'email_or_mobile' => $emailOrMobile,
            'is_new_user' => $is_new_user,
            'verification_code' => $verificationCode['data']
        ];
        if($is_new_user) $response['redirect'] = route('users.v1_get_profile');
        return responseGenerator()->success($response);
    }


    /**
     * login with password function
     *
     * @param String $emailOrMobile
     * @param Request $request
     * @return array
     */
    public function loginWithPassword($emailOrMobile, $request)
    {
        $credentials1 = ['email' => $emailOrMobile, 'password' => $request['password']];
        $credentials2 = ['mobile' => $emailOrMobile, 'password' => $request['password']];

        if (!auth()->attempt($credentials1) && !auth()->attempt($credentials2)) {
            return responseGenerator()->forbidden(array(
                'errors' => array(
                    'invalid' => __('validation.user_or_password_invalid')
                )
            ));
        }
        $user = auth()->user();
        $response = array(
            'token' => 'Bearer ' . $user->createToken('Blog', ['blog-user'])->accessToken,
            'profile' => [new UserResource($user)]
        );

        return responseGenerator()->success($response);
    }

    /**
     * Verify function
     *
     * @param Request $request
     * @return array
     */
    public  function verify($request){

        $emailOrMobile = helper()->enNum($request['email_or_mobile']);
        $emailOrMobile = str_replace(['(', ')', '+'], ['', '', ''], $emailOrMobile);


        // check login field
        $login_type = filter_var($emailOrMobile, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';


        $user = null;
        if($login_type == 'email')
            $user = $this->user->where('email', $emailOrMobile)->first();
        else
            $user = $this->user->where('mobile', $emailOrMobile)->first();



        if(empty($user)){
            //user not found
            return responseGenerator()->notfound(array(
                'errors' => array(
                    'notfound' => __('validation.user_not_found')
                )
            ));
        }

        $lastVerificationCode = logActivity()->getUserVerificationCode($user->id);


        if(empty($lastVerificationCode)){
            return responseGenerator()->forbidden(array(
                'errors' => array(
                    'notfound' => __('validation.user_not_found')
                )
            ));
        }

        if(Carbon::now()->diffInSeconds(new Carbon($lastVerificationCode->created_at)) > config("blog.otp_verify_code_expire_time", 360)){
            return responseGenerator()->forbidden(array(
                'errors' => array(
                    'verify_code_expired' => __('validation.verify_code_expired')
                )
            ));
        }

        if ($lastVerificationCode['data']['data'] != $request['verification_code']) {

            return responseGenerator()->forbidden(array(
                'errors' => array(
                    'verify_code' => __('validation.verify_code_wrong')
                )
            ));
        }

        $logs = logActivity()->LoginLog();
        logActivity()->insert($logs, $user->id);


        $response = array(
            'token' => 'Bearer ' . $user->createToken('Blog', ['blog-user'])->accessToken,
            'profile' => [new UserResource($user)]
        );


        return responseGenerator()->success($response);
    }

    /**
     * logout function
     *
     * @param Request $request
     * @return array
     */
    public  function logout($request){
        try{
            $token = $request->user()->token();
            $token->revoke();
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
        return responseGenerator()->success(array('logout' => true));
    }



    /**
     * register function
     *
     * @param String $emailOrMobile
     * @param String $col
     * @return User Model
     */
    public function register($emailOrMobile, $col = 'mobile'){
        $user = $this->user->create([$col => $emailOrMobile]);
        return $user;
    }

}
