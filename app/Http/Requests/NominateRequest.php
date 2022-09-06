<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NominateRequest extends BaseRequest
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
            "name" => "required|string",
            'email' => 'required|email',
            'product_name' => 'required|string',
            'where_link' => 'required|string',
            "image" => 'required|string',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            "contact_phone" => 'required|numeric'
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationValidation(): array
    {
        return [
            "name" => "required|string",
            'email' => 'required|email',
            'product_name' => 'required|string',
            'where_link' => 'required|string',
            "image" => 'required|string',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            "contact_phone" => 'required|numeric',
        ];
    }
}
