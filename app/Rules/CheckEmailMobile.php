<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckEmailMobile implements Rule
{
    protected  $value;
    protected  $isEmail;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = '';
        $this->isEmail = false;
    }


    /**
     * check is mobile or email
     *
     * @param string $value
     * @return bool
     */
    public function check($value)
    {
        $this->value = $value;
        $value = helper()->enNum($value);
        $value = str_replace(['(', ')', '+'], ['', '', ''], $value);

        // check login field
        $login_type = filter_var($value, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';


        if($login_type == 'mobile'){

            if(!is_numeric($value)){
                $this->isEmail = true;
                return false;
            }
            if ((bool) preg_match('/^(((98)|(\+98)|(0098)|0)(9){1}[0-9]{9})+$/', $value) || (bool) preg_match('/^(9){1}[0-9]{9}+$/', $value)) {
                return true;
            }
            return false;
        }
        $this->isEmail = true;
        return true;

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->check($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->isEmail ? trans('validation.email', [':attribute' => $this->value]) : trans('validation.mobile', [':attribute' => $this->value]);
    }
}
