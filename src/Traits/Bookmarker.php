<?php

/*
 * This file is part of the trendsoft/laravel-bookmark
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENER.
 */

namespace LaravelBookmark\Traits;

use Illuminate\Database\Eloquent\Model;

trait Bookmarker
{
    /**
     * @param Model $model
     * @return null
     */
    public function bookmark(Model $model)
    {
        if (!$this->hasBookmarked($model)) {
            $bookmark = app(config('bookmark.bookmark_model'));
            $bookmark->{config('bookmark.user_foreign_key')} = $this->getKey();

            $model->bookmarks()->save($bookmark);
        }
        return null;
    }

    /**
     * @param Model $model
     */
    public function unBookmark(Model $model)
    {
        $relation = $model->bookmarks()->where('bookmarkable_id', $model->getKey())->where('bookmarkable_type', $model->getMorphClass())->where(config('bookmark.user_foreign_key'), $this->getKey())->first();
        if ($relation) {
            $relation->delete();
        }
    }

    /**
     * @param Model $model
     * @return void|null
     */
    public function toggleBookmark(Model $model)
    {
        return $this->hasBookmarked($model) ? $this->unBookmark($model) : $this->bookmark($model);
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function hasBookmarked(Model $model)
    {
        return ($this->relationLoaded('bookmarks') ? $this->bookmarks : $this->bookmarks())->where('bookmarkable_id', $model->getKey())->where('bookmarkable_type', $model->getMorphClass())->count() > 0;
    }

    /**
     * @return mixed
     */
    public function bookmarks()
    {
        return $this->hasMany(config('bookmark.bookmark_model'), config('bookmark.user_foreign_key'), $this->getKeyName());
    }
}
