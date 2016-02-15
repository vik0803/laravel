<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Banner;

class BannerRequest extends Request
{
    protected $rules = [
        'name' => 'required|max:255',
        'slug' => 'required|max:255|unique:banners',
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
    public function rules(Banner $banner)
    {
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $banner = Banner::findOrFail(\Request::input('id'))->first();

            array_forget($this->rules, 'slug');
            $this->rules = array_add($this->rules, 'slug', 'required|max:255|unique:banners,slug,' . $banner->id);
        }

        return $this->rules;
    }
}
