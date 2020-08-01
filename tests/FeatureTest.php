<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Support\Facades\Event;

class FeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        config(['auth.providers.users.model' => User::class]);
    }

}