<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TalentPoolRequest extends BaseRequest
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
        }
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleCreationValidation(): array
    {
        return [
            "name" => "required|string",
            'email' => 'required|email|unique:talent_pools,email',
            'address' => 'required|string',
            'phone' => 'required|numeric',
            "alt_phone" => 'sometimes|numeric',
            "linkedin_profile" => "required|string",
            'cv' => 'required|string',
            'profession' => 'required|string',
            'what_you_do' => 'required|string',
            "contributions" => 'required|array',
            "contributions.*.name" => 'required|string|exists:contribution_types,name',
            "previous_project" => "required|string",
            'coordinate_answer' => 'required|string|in:Yes,No,Maybe',
            'mentor_answer' => 'required|string|in:Yes,No',
            'agreed_amount' => 'required|numeric',
            "account_name" => 'required|string',
            "account_number" => 'required|numeric',
            "bank_code" => 'required|string',
            "other_payment_address" => 'required|string',
            "country_id" => 'required|integer|exists:countries,id',
            "bank_id" => 'required|integer|exists:banks,id',
            "currency" => 'required|string|exists:currencies,name'
        ];
    }
}
