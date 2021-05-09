<?php

namespace Wink\Traits;

trait HasSlug
{
    /**
     * Find model by slug.
     *
     * @param string $slug
     * @return Illuminate\Database\Eloquent\Model|null $post|null
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }
}
