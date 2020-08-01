<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace LaravelBookmark;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelBookmark\Events\Bookmarked;
use LaravelBookmark\Events\Unbookmarked;

class Bookmark extends Model
{
    protected $dispatchesEvents = [
        'created' => Bookmarked::class,
        'deleted' => Unbookmarked::class
    ];

    /**
     * Bookmark constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('bookmark.bookmarks_table');
        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();
        self::saving(function ($bookmark) {
            $userForeignKey = config('bookmark.user_foreign_key');
            $bookmark->{$userForeignKey} = $bookmark->{$userForeignKey} ?: auth()->id();
        });
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), config('bookmark.user_foreign_key'));
    }

    /**
     * @return BelongsTo
     */
    public function bookmarker()
    {
        return $this->user();
    }

    /**
     * @param Builder $builder
     * @param string $type
     * @return Builder
     */
    public function scopeWithType(Builder $builder, string $type)
    {
        return $builder->where('bookmarkable_type', app($type)->getMorphClass());
    }
}