<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannerImage extends Model
{
    protected $fillable = [
        'name', 'description', 'url', 'identifier', 'file', 'uuid', 'extension', 'size', 'order', 'page_id',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
