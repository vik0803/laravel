<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateDomainRequest extends Request
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
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:domains',
            'locales' => 'required|array',
            'default_locale_id' => 'required|numeric',
        ];
    }
}
