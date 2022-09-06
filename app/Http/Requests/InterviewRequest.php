<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterviewRequest extends BaseRequest
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
            'media_link' => 'sometimes',
            'image' => 'sometimes',
            'interview_text' => 'sometimes',
            "category_id" => 'required|integer|exists:categories,id',
            "interview_type_id" => 'required|integer|exists:interview_types,id',
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
            'media_link' => 'required|string',
            'image' => 'sometimes',
            'interview_text' => 'sometimes',
            "category_id" => 'required|integer|exists:categories,id',
            "interview_type_id" => 'required|integer|exists:interview_types,id',
        ];
    }
}
