<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsInTenant implements ValidationRule
{
    public function __construct(private mixed $model, private string $type = 'id'){}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $model = app($this->model);
        if(!$model->where($this->type, $value)->exists()) {
            $fail("{$value} does not exists as {$this->type} in {$model->getTable()}");
        }
    }
}
