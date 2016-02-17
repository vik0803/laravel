<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Nav;

class NavRequest extends Request
{
    protected $rules = [
        'name' => 'required|max:255',
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
    public function rules(Nav $page)
    {
        $this->merge(['is_dropdown' => $this->input('is_dropdown', 0)]); // set default value of the is_dropdown checkbox

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $page = Nav::findOrFail(\Request::input('id'))->first();
            $parent = $page->parent;

            $this->rules = array_add($this->rules, 'slug', 'required|max:255|unique:nav,slug,' . $page->id . ',id,parent,' . ($parent ?: 'NULL'));
        } else {
            $parent = \Request::session()->get($page->getTable() . 'Parent', 0);

            $this->rules = array_add($this->rules, 'slug', 'required|max:255|unique:nav,slug,NULL,id,parent,' . ($parent ?: 'NULL'));
        }

        return $this->rules;
    }
}
