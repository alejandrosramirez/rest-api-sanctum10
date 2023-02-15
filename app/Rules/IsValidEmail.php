<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsValidEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/^[\w.-]+@\w+(\.[a-z]{2,})+$/i', $value)) {
            $fail('validation.custom.is_valid_email')->translate([
                'attribute' => trans('validation.attributes.email'),
            ]);
        }
    }
}
