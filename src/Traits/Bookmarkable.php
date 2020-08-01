<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace LaravelBookmark\Traits;

use Illuminate\Database\Eloquent\Model;

trait Bookmarkable
{
    /**
     * @param Model $user
     * @return bool
     */
    public function isBookmarkedBy(Model $user)
    {
        if (is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('bookmarkers')) {
                return $this->bookmarkers->contains($user);
            }
            return ($this->relationLoaded('bookmarks') ? $this->bookmarks : $this->bookmarks())->where(config('bookmark.user_foreign_key'), $user->getKey())->count() > 0;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function bookmarks()
    {
        return $this->morphMany(config('bookmark.bookmark_model'), 'bookmarkable');
    }

    /**
     * @return mixed
     */
    public function bookmarkers()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), config('bookmark.bookmarks_table'), 'bookmarkable_id', config('bookmark.user_foreign_key'))->where('bookmarkable_type', $this->getMorphClass());
    }
}