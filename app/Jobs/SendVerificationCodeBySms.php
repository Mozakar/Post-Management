<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendVerificationCodeBySms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!isset($this->user->mobile)) {
            return false;
        }
        $lastVerificationCode = logActivity()->getUserVerificationCode($this->user->id);
        if (empty($lastVerificationCode)) {
            return false;
        }
        if (Carbon::now()->diffInSeconds(new Carbon($lastVerificationCode->created_at)) > config("blog.otp_resend_wait_time", 120)) {
            return false;
        }
        $code = $lastVerificationCode['data']['data'];

      //  Sms::sendSms($this->user->mobile, $code);
    }

}
