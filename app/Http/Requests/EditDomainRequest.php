<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Domain;

class EditDomainRequest extends Request
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
        $this->merge(['hide_default_locale' => $this->input('hide_default_locale', 0)]); // set default value of the hide_default_locale checkbox

        $domain = Domain::findOrFail(\Request::input('id'))->first();

        return [
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:domains,slug,' . $domain->id,
            'locales' => 'required|array',
            'default_locale_id' => 'required|numeric',
        ];
    }
}
