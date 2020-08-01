<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use LaravelBookmark\Traits\Bookmarker;

class User extends Model
{
    use Bookmarker;

    protected $fillable = ['name'];
}