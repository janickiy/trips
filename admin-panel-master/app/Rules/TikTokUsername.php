<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TikTokUsername implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if(strlen($value) < 1) {
            return false;
        }
        
        if(!ctype_alpha($value[0])) {
            return false;
        }
        
        if(strtolower($value) !== $value) {
            return false;
        }
        
        $length = strlen($value);
        
        if($value[$length - 1] ===  '.' or $value[$length - 1] ===  '_') {
            return false;
        }
        
        return preg_match('/^[a-z0-9_.]+$/i', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Wrong username.';
    }
}
