<?php
/**
 * This file is part of the trendsoft/laravel-bookmark.
 * (c) trendsoft <hadi@trendsoft.org>
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Trendsoft\LaravelBookmark\Events\Bookmarked;
use Trendsoft\LaravelBookmark\Events\Unbookmarked;

class FeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        config(['auth.providers.users.model' => User::class]);
    }

    public function test_basic_features()
    {
        $user = User::create(['name' => 'trendsoft']);
        $post = Post::create(['title' => 'test post']);

        $user->bookmark($post);

        Event::assertDispatched(Bookmarked::class, function ($event) use ($user, $post) {
            return $event->bookmark->bookmarkable instanceof Post && $event->bookmark->user instanceof User && $event->bookmark->user->id === $user->id && $event->bookmark->bookmarkable->id === $post->id;
        });

        $this->assertTrue($user->hasBookmarked($post));
        $this->assertTrue($post->isBookmarkedBy($user));

        $this->assertNull($user->unBookmark($post));

        Event::assertDispatched(Unbookmarked::class, function ($event) use ($user, $post) {
            return $event->bookmark->bookmarkable instanceof Post && $event->bookmark->user instanceof User && $event->bookmark->user->id === $user->id && $event->bookmark->bookmarkable->id === $post->id;
        });
    }

    public function test_unbookmark_features()
    {
        $user1 = User::create(['name' => 'trendsoft']);
        $user2 = User::create(['name' => 'trend']);
        $user3 = User::create(['name' => 'soft']);

        $post = Post::create(['title' => 'test post']);

        $user1->bookmark($post);
        $user2->bookmark($post);
        $user3->bookmark($post);

        $user1->unBookmark($post);

        $this->assertFalse($user1->hasBookmarked($post));
        $this->assertTrue($user2->hasBookmarked($post));
        $this->assertTrue($user3->hasBookmarked($post));
    }

    public function test_aggregations()
    {
        $user = User::create(['name' => 'trendsoft']);

        $post1 = Post::create(['title' => 'test post 1']);
        $post2 = Post::create(['title' => 'test post 2']);
        $book1 = Book::create(['title' => 'test book 1']);
        $book2 = Book::create(['title' => 'test book 2']);

        $user->bookmark($post1);
        $user->bookmark($post2);
        $user->bookmark($book1);
        $user->bookmark($book2);

        $this->assertSame(4, $user->bookmarks()->count());
        $this->assertSame(2, $user->bookmarks()->withType(Book::class)->count());
    }

    public function test_object_bookmarkers()
    {
        $user1 = User::create(['name' => 'trendsoft']);
        $user2 = User::create(['name' => 'trend']);
        $user3 = User::create(['name' => 'soft']);

        $post = Post::create(['title' => 'test post']);

        $user1->bookmark($post);
        $user2->bookmark($post);

        $this->assertCount(2, $post->bookmarkers);

        $this->assertSame('trendsoft', $post->bookmarkers[0]['name']);
        $this->assertSame('trend', $post->bookmarkers[1]['name']);

        $sqls = $this->getQueryLog(function () use ($post, $user1, $user2, $user3) {
            $this->assertTrue($post->isBookmarkedBy($user1));
            $this->assertTrue($post->isBookmarkedBy($user2));
            $this->assertFalse($post->isBookmarkedBy($user3));
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_object_bookmarkers_with_custom_morph_class_name()
    {
        $user1 = User::create(['name' => 'trendsoft']);
        $user2 = User::create(['name' => 'trend']);
        $user3 = User::create(['name' => 'soft']);

        $post = Post::create(['title' => 'test post']);

        Relation::morphMap(['posts' => Post::class]);

        $user1->bookmark($post);
        $user2->bookmark($post);

        $this->assertCount(2, $post->bookmarkers);

        $this->assertSame('trendsoft', $post->bookmarkers[0]['name']);
        $this->assertSame('trend', $post->bookmarkers[1]['name']);
    }

    public function test_eager_loading()
    {
        $user = User::create(['name' => 'trendsoft']);

        $post1 = Post::create(['title' => 'test post 1']);
        $post2 = Post::create(['title' => 'test post 2']);
        $book1 = Book::create(['title' => 'test book 1']);
        $book2 = Book::create(['title' => 'test book 2']);

        $user->bookmark($post1);
        $user->bookmark($post2);
        $user->bookmark($book1);
        $user->bookmark($book2);

        $sqls = $this->getQueryLog(function () use ($user) {
            $user->load('bookmarks.bookmarkable');
        });

        $this->assertSame(3, $sqls->count());

        $sqls = $this->getQueryLog(function () use ($user, $post1) {
            $user->hasBookmarked($post1);
        });

        $this->assertEmpty($sqls->all());
    }

    /**
     * @param Closure $callback
     * @return Collection
     */
    protected function getQueryLog(Closure $callback): Collection
    {
        $sqls = collect([]);
        DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });

        $callback();
        return $sqls;
    }
}