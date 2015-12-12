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
        $domain = Domain::findOrFail(\Request::input('id'))->first();

        return [
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:domains,slug,' . $domain->id,
            'locales' => 'required',
        ];
    }
}
