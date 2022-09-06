<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreConsultationRequest extends BaseRequest
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
            'email' => 'required|email',
            'company_name' => 'required|string',
            'phone' => 'required|numeric',
            "address" => 'required|string',
            "communication_type" => 'required|array',
            "communication_type.*.name" => 'required|string|exists:communication_types,name',
            "about_business" => "required|string",
            'achievement' => 'required|string',
            'expectation' => 'required|string',
            'goals' => 'required|string',
            "constraints" => 'required|string',
            "outcomes" => "required|string",
            'target' => 'required|string',
            'areas_of_need' => 'required|array',
            'areas_of_need.*.name' => 'required|string|exists:contribution_types,name',
            'budget' => 'required|numeric',
            "timeline" => 'required|string',
            "questions" => 'required|string',
        ];
    }
}
