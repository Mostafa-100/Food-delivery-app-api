<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Phone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^(\+?[0-9]{1,3})?[-. ]?(\(?[0-9]{3}\)?)[-. ]?[0-9]{3}[-. ]?[0-9]{4}$/';
        if (!preg_match($pattern, $value)) {
            $fail('The :attribute must be a valid phone number');
        }
    }
}
