<?php

namespace Wink;

class WinkTag extends AbstractWinkModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wink_tags';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The posts that has the tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(WinkPost::class, 'wink_posts_tags', 'tag_id', 'post_id');
    }

    /**
     * Scope a query to only include tags with a name that starts with '#'.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivate($builder)
    {
        return $builder->where('name', 'LIKE', '#%');
    }

    /**
     * Scope a query to only include tags with a name that doesn't start with '#'.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($builder)
    {
        return $builder->where('name', 'NOT LIKE', '#%');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            $item->posts()->detach();
        });
    }
}
