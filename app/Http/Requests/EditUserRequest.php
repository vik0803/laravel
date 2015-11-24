<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\User;

class EditUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = User::findOrFail(\Request::input('id'))->first();

        return [
            'group' => 'required',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => array('required', 'confirmed', 'regex:/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*[\p{N}\p{P}]).{6,}$/u'), // /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).{6,}$/ // http://www.zorched.net/2009/05/08/password-strength-validation-with-regular-expressions/
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'password.regex' => trans('passwords.regex'),
        ];
    }
}
