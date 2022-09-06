<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class JobPostRequest extends BaseRequest
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
            "title" => "required|string",
            'company_name' => 'required|string',
            'location_id' => 'required|integer|exists:countries,id',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'duration_type_id' => 'required|integer|exists:duration_types,id',
            "descriptions" => 'required|string',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            "summaries" => "required|string",
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationValidation(): array
    {
        return [
            "title" => "required|string",
            'company_name' => 'required|string',
            'location_id' => 'required|integer|exists:countries,id',
            'job_type_id' => 'required|integer|exists:job_types,id',
            'duration_type_id' => 'required|integer|exists:duration_types,id',
            "descriptions" => 'required|string',
            'responsibilities' => 'required|string',
            'requirements' => 'required|string',
            "summaries" => "required|string",
        ];
    }
}
