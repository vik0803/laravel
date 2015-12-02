<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateLocaleRequest extends Request
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
        return [
            'locale' => 'required|size:2|unique:locales',
            'name' => 'required|max:255',
            'native' => 'required|max:255',
            'script' => 'required|size:3',
        ];
    }
}
