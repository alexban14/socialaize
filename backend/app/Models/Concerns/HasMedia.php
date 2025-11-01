<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')->withPivot('tag');
    }

    public function getAvatarAttribute()
    {
        return $this->media()->wherePivot('tag', 'avatar')->first()?->url;
    }

    public function getCoverImageAttribute()
    {
        return $this->media()->wherePivot('tag', 'cover_image')->first()?->url;
    }
}
