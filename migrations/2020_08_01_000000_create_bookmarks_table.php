<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarksTable extends Migration
{
    public function up()
    {
        Schema::create(config('bookmark.bookmarks_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger(config('bookmark.user_foreign_key'))->index()->comment('user_id');
            $table->morphs('bookmarkable');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('bookmark.bookmarks_table'));
    }
}