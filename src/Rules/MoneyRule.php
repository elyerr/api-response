<?php

namespace Elyerr\ApiResponse\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MoneyRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if (!preg_match('/^[\d.,]+$/', $value)) {
            $fail(__('The :attribute field must be a valid amount.'));
            return;
        }

        $normalized = str_replace(['.', ','], '', $value);

        request()->merge([
            $attribute => (int) $normalized
        ]);
    }

}
