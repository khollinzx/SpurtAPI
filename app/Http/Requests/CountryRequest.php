<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends BaseRequest
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
            "name" => [
                "required",
                function ($key, $value, $fn) {
                    if (Country::getCountryByName(ucwords($value)))
                        return $fn('This country already exist.');
                }
            ],
            'slug' => 'required|string',
            'code' => 'required|string',
            'digit_length' => 'required|integer',
            "currency_id" => 'required|integer|exists:currencies,id',
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
            'slug' => 'required|string',
            'code' => 'required|string',
            'digit_length' => 'required|integer',
            "currency_id" => 'required|integer|exists:currencies,id',
        ];
    }
}
