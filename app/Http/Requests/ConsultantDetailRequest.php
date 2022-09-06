<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\UserDetail;
use Illuminate\Foundation\Http\FormRequest;

class ConsultantDetailRequest extends BaseRequest
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
        $user = (new Controller())->getUser();
        return [
            "address" => "required|string",
            'business_name' => 'required|string',
            'bank_id' => 'required|integer|exists:banks,id',
            'phone' => [
                "required",
                "numeric",
                function ($k, $v, $fn) use ($user) {
                    $country = Country::find($user->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (UserDetail::checkIfNumberExist($k, $v))
                        return $fn('An account with same phone number already exist.');
                }
            ],
            'other_payment_address' => 'required|string',
            'agreed_amount' => 'required|float',
            'account_name' => 'required|string',
            'account_number' => 'required|numeric',
            'bank_code' => 'required|string',
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
            "address" => "required|string",
            'bank_id' => 'required|integer|exists:banks,id',
            'phone' => [
                "required",
                "numeric",
                function ($k, $v, $fn) use ($user) {
                    $country = Country::find($user->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (UserDetail::checkIfNumberExistElseWhere($k, $v, $user->id))
                        return $fn('An account with same phone number already exist.');
                }
            ],
            'other_payment_address' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|numeric',
            'bank_code' => 'required|string',
        ];
    }
}
