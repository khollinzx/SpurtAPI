<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends BaseRequest
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
                'required',
                'string',
                Rule::unique('reviews', ucwords('name'))
            ],
            'where' => 'required|string',
            'product_quantity' => 'required|integer',
            'product_packaging' => 'required|string',
            "shelf_life" => 'required|numeric',
            "shipping" => "required|string",
            'customer_service' => 'required|string',
            'content' => 'required|string',
            'general_review' => 'required|string',
            'made_in_score' => 'required|numeric',
            "uploads" => 'required|array',
//            "uploads.*.image" => "required|string",
            "product_type_id" => 'required|integer|exists:product_types,id',
            "country_id" => 'required|integer|exists:countries,id'
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
            'where' => 'required|string',
            'product_quantity' => 'required|integer',
            'product_packaging' => 'required|string',
            "shelf_life" => 'required|numeric',
            "shipping" => "required|string",
            'customer_service' => 'required|string',
            'content' => 'required|string',
            'general_review' => 'required|string',
            'made_in_score' => 'required|numeric',
            "product_type_id" => 'required|integer|exists:product_types,id',
            "country_id" => 'required|integer|exists:countries,id'
        ];
    }
}
