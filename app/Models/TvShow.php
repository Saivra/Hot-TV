<?php

namespace App\Models;
use Carbon\Carbon;

class TvShow extends BaseModel
{

    protected $casts = ['tags' => 'array'];

    public function categories(){
        return $this->belongsToMany(ShowCategory::class);
    }

    public function casts(){
        return $this->belongsToMany(Cast::class);
    }

    public function releaseAt(){
        return Carbon::parse($this->release_date)->format('M d, Y');
    }

    public function episodes(){
        return $this->hasMany(Episode::class);
    }

    public function views(){
        return $this->hasMany(TvShowView::class, 'tv_show_id');
    }

    public function watchlists()
    {
        return $this->morphMany(Watchlist::class, 'watchable');
    }
}
