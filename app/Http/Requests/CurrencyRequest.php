<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method())
        {
            case "POST":
                return $this->handleCreationValidation();

            case "PATCH":
                return $this->handleModificationValidation();
        }
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleCreationValidation(): array
    {
        return [
            "name" => 'required|string|unique:currencies,name',
            'sign' => 'required|string'
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationValidation(): array
    {
        return [
            "name" => 'required|string',
            'sign' => 'required|string'
        ];
    }
}
