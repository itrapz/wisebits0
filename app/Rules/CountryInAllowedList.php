<?php

namespace App\Rules;

use App\Model\Country;
use Illuminate\Contracts\Validation\Rule;

class CountryInAllowedList implements Rule
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
        return in_array($value, Country::ALLOWED_COUNTRIES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'received country is not in allowed list';
    }
}
