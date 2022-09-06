<?php

namespace App\Http\Requests;

use App\Models\ProductType;
use App\Models\RequestDemo;
use App\Models\RequestService;
use App\Models\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends BaseRequest
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
            'bill_to' => 'required|integer|exists:users,id',
            'date' => 'required|string',
            'due_date' => 'required|string',
            'note' => 'required|string',
            'description' => 'required|string',
            'request_tag_no' => 'sometimes',
            'bill_to_name' => 'sometimes',
            'preferred_currency_id' => 'required|integer|exists:currencies,id',
            'payment_type_id' => 'required|integer|exists:payment_types,id',
            'sub_total' => 'required|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.type' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.amount' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.quantity' => 'required|integer'
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
            'image' => 'nullable'
        ];
    }
}
