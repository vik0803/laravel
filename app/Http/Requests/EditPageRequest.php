<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Page;

class EditPageRequest extends Request
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
        $page = Page::findOrFail(\Request::input('id'))->first();
        $parent = $page->parent;

        return [
            'name' => 'required|max:255',
            'slug' => 'max:255|unique:pages,slug,' . $page->id . ',id,parent,' . $parent,
        ];
    }
}
