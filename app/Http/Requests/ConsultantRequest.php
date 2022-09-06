<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\ConsultantDetail;
use App\Models\Country;
use App\Models\UserDetail;
use Illuminate\Foundation\Http\FormRequest;

class ConsultantRequest extends BaseRequest
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
            "first_name" => "required|string",
            'last_name' => 'required|string',
            'email' => 'required|email|unique:consultants,email',
            'password' => 'required|confirmed|min:8',
            "country_id" => 'required|integer|exists:countries,id',
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationValidation(): array
    {
        $user = (new Controller())->getUser();
        return [
            "name" => "required|string",
            'image' => 'nullable',
            "address" => "required|string",
            'bank_id' => 'required|integer|exists:banks,id',
            'phone' => [
                "required",
                "numeric",
                function ($k, $v, $fn) use ($user) {
                    $country = Country::find($user->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (ConsultantDetail::checkIfNumberExistElseWhere($k, $v, $user->id))
                        return $fn('An account with same phone number already exist.');
                }
            ],
            'other_payment_address' => 'sometimes|string',
            'account_name' => 'required|string',
            'account_number' => 'required|numeric',
            'bank_code' => 'string',
        ];
    }
}
