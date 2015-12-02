<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Locale;

class EditLocaleRequest extends Request
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
        $locale = Locale::findOrFail(\Request::input('id'))->first();

        return [
            'locale' => 'required|size:2|unique:locales,locale,' . $locale->id,
            'name' => 'required|max:255',
            'native' => 'required|max:255',
            'script' => 'required|size:3',
        ];
    }
}
