<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [];

        switch (basename($this->url()))
        {
            case "create":
                $validation = $this->handleCreationValidation();
                break;

            case "update":
                $validation = $this->handleModificationValidation();
                break;
        }

        return $validation;
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleCreationValidation(): array
    {
        return [
            "title" => "required|string",
            'sub_title' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            "uploads" => 'required|array',
            //'uploads.*.image' => 'required|string'
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
            'sub_title' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            "uploads" => 'required|array',
            //'uploads.*.image' => 'required|string'
        ];
    }
}
