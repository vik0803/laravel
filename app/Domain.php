<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Get the locales for the domain.
     */
    public function locales()
    {
        return $this->belongsToMany(Locale::class)->withTimestamps()->withPivot('default');
    }
}
