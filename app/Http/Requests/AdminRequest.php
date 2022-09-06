<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseRequest
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
            case "admin":
                $validation = $this->handleCreationValidation();
                break;

            case "profile":
                $validation = $this->handleModificationValidation();
                break;

            case "image":
                $validation = $this->handleImageModificationValidation();
                break;

            case "password":
                $validation = $this->handleUpdatePasswordValidation();
                break;

            case "editPrivileges":
                $validation = $this->handleModificationPrivileges();
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
            "name" => 'required|string',
            'email' => 'required|email|unique:admins,email',
            "country_id" => 'required|integer|exists:countries,id',
            "phone" => [
                "required",
                function ($k, $v, $fn) {
                    $country = Country::find($this->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (Admin::checkIfNumberExist($k, $v))
                        return $fn('An account with same phone number already exist.');
                }
            ],
            'role_id' => 'required|integer|exists:roles,id',
//            "platform_type_id" => "required|integer|exists:platform_types,id",
            "menus" => "required|array",
            'menus.*.menu_id' => 'required|integer|exists:menu_bars,id'
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationValidation(): array
    {
        $user = Admin::find((new Controller())->getUserId());
        return [
            "name" => 'required|string',
            "phone" => [
                "required",
                function ($k, $v, $fn) use ($user) {
                    $country = Country::find($user->country_id);
                    if(strlen($v) !== $country->digit_length)
                        return $fn("Phone number must be $country->digit_length digits");

                    if (Admin::checkIfNumberExistElseWhere($user->id, $k, $v))
                        return $fn('An account with same phone number already exist.');
                }
            ]
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleUpdatePasswordValidation(): array
    {
        return [
            'password' => 'required|min:8',
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleImageModificationValidation(): array
    {
        return [
            'image' => 'required|string',
        ];
    }

    /**
     * This handles the User modification
     * @return array
     */
    public function handleModificationPrivileges(): array
    {
        $user = Admin::find($this->admin_id);
        return [
            "name" => 'required|string',
            'email' => "required|email|unique:admins,phone,$this->admin_id,id",
            "phone" => [
                "required",
                'numeric',
                "unique:admins,phone,$this->admin_id,id",
                function ($k, $v, $fn) use ($user) {
                    $country = Country::find($user->country_id);
                    $length = strlen($country->code);
                    if(substr($v,0,1) == "+")
                        return $fn("Kindly remove the + sign, then submit again");

                    if((int)substr($v,0, $length) !== $country->code)
                        return $fn("kindly attach the country code $country->code to your phone number");

                    if (Admin::checkIfNumberExistElseWhere($user->id, $k, $v))
                        return $fn('An account with same phone number already exist.');
                }
            ],
            'role_id' => 'required|integer|exists:roles,id',
            "menus" => "required|array",
            'menus.*.menu_id' => 'required|integer|exists:menu_bars,id'
        ];
    }
}
