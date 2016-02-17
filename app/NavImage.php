<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NavImage extends Model
{
    protected $fillable = [
        'name', 'title', 'file', 'uuid', 'extension', 'size', 'order', 'nav_id',
    ];

    public function page()
    {
        return $this->belongsTo(Nav::class);
    }
}
