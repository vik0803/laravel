<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Page;

class CreatePageRequest extends Request
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
    public function rules(Page $page)
    {
        $parent = \Request::session()->get($page->getTable() . 'Parent', 0);

        return [
            'name' => 'required|max:255',
            'slug' => 'max:255|unique:pages,slug,null,id,parent,' . $parent,
        ];
    }
}
