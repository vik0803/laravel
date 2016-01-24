<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageImage extends Model
{
    protected $fillable = [
        'name', 'title', 'file', 'uuid', 'extension', 'size', 'order', 'page_id',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
