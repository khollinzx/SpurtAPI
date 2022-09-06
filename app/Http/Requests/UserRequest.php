<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;

class UserRequest extends BaseRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            "country_id" => 'required|integer|exists:countries,id',
            'phone' => [
                "required",
                "numeric",
                function ($k, $v, $fn) {
                    $country = Country::find($this->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (UserDetail::checkIfNumberExist($k, $v))
                        return $fn('An account with same phone number already exist.');
                }
            ]
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
            ]
        ];
    }
}
