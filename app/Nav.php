<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nav extends Model
{
    protected $table = 'nav';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'title', 'slug', 'description', 'content', 'is_category', 'is_dropdown', 'order', 'parent',
    ];

    public function images()
    {
        return $this->hasMany(NavImage::class);
    }
}
