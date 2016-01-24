<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Domain;

class DomainRequest extends Request
{
    protected $rules = [
        'name' => 'required|max:255',
        'slug' => 'required|max:255|unique:domains',
        'locales' => 'required|array',
        'default_locale_id' => 'required|numeric',
    ];

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

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $domain = Domain::findOrFail(\Request::input('id'))->first();

            array_forget($this->rules, 'slug');
            $this->rules = array_add($this->rules, 'slug', 'required|max:255|unique:domains,slug,' . $domain->id);
        }

        return $this->rules;
    }
}
