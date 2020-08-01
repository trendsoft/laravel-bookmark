<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace LaravelBookmark\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    public $bookmark;

    /**
     * Event constructor.
     * @param Model $bookmark
     */
    public function __construct(Model $bookmark)
    {
        $this->bookmark = $bookmark;
    }
}