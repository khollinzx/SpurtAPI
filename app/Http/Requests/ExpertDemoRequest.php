<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpertDemoRequest extends BaseRequest
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

            case "PUT":
                return $this->handleModificationValidationPUT();
        }
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleCreationValidation(): array
    {
        return [
            "products" => 'required|array',
            "products.*.name" => 'required|string|exists:product_types,name',
            "bundle_type_id" => 'required|integer|exists:bundle_types,id',
//            "platform_type_id" => 'required|integer|exists:platform_types,id',
            "country_id" => 'required|integer|exists:countries,id',
            'date' => 'required|string',
            'time' => 'required|string',
            "first_name" => "required|string",
            "last_name" => "required|string",
            'email' => 'required|email',
            'phone' => 'required|string'
        ];
    }

    /**
     * This handles the User creation validation
     * @return array
     */
    public function handleModificationValidationPUT(): array
    {
        return [
            "consultants" => 'required|array',
            "consultants.*.id" => 'required|integer|exists:consultants,id',
            "assigned_admin_id" => 'required|integer|exists:admins,id',
        ];
    }
}
