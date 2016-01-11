<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Page;

class PageRequest extends Request
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
    public function rules(Page $page)
    {
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $page = Page::findOrFail(\Request::input('id'))->first();
            $parent = $page->parent;

            array_add($this->rules, 'slug', 'max:255|unique:pages,slug,' . $page->id . ',id,parent,' . $parent);
        } else {
            $parent = \Request::session()->get($page->getTable() . 'Parent', 0);

            array_add($this->rules, 'slug', 'max:255|unique:pages,slug,null,id,parent,' . $parent);
        }

        return $this->rules;
    }
}
