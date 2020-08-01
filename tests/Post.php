<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Trendsoft\LaravelBookmark\Traits\Bookmarkable;

class Post extends Model
{
    use Bookmarkable;

    protected $fillable = ['title'];
}