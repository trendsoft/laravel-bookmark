Laravel bookmark
---
User bookmark feature for Laravel Application.

![CI](https://github.com/trendsoft/laravel-bookmark/workflows/CI/badge.svg)

## Installing

```shell
$ composer require trendsoft/laravel-bookmark -vvv
```

## Configuration

This step is optional

```shell
$ composer artisan vendor:publish --provider="Trendsoft\\LaravelBookmark\\BookmarkServiceProvider" --tag=config
```

## Migrations

This step is also optional, if you want to custom bookmarks table, you can publish the migration files:

```shell
$ composer artisan vendor:publish --provider="Trendsoft\\LaravelBookmark\\BookmarkServiceProvider" --tag=migrations
```

# Usage

## Traits

`Trendsoft\LaravelBookmark\Traits\Bookmarker`

```php
use Illuminate\Database\Eloquent\Model;
use Trendsoft\LaravelBookmark\Traits\Bookmarker;

class User extends Model
{
    use Bookmarker;
}
```

`Trendsoft\LaravelBookmark\Traits\Bookmarkable`

```php
use Illuminate\Database\Eloquent\Model;
use Trendsoft\LaravelBookmark\Traits\Bookmarkable;

class Post extends Model
{
    use Bookmarkable;
}
```

## API

```php
$user = User::find(1);
$post = Post::find(1);

$user->bookmark($post);
$user->unBookmark($post);
$user->toggleBookmark($post);

$user->hasBookmarked($post);
$post->isBookmarkedBy($user);
```

Get user bookmarks with pagination:

```php
$bookmarks = $user->bookmarks()->with('bookmarkable')->paginate(20);

foreach($bookmarks as $bookmark){
    $bookmark->bookmarkable; // App\Post instance
}
```

Get object bookmarkers:

```php
foreach($post->bookmarkers as $user){
    echo $user->name;
}
```

with pagination:

```php
$bookmarkers = $post->bookmarkers()->paginate(20);
foreach($bookmarkers as $user){
    echo $user->name;
}
```

## Aggregations

```php
//all
$user->bookmarks()->count();

//with type
$user->bookmarks()->withType(Post::class)->count();

// bookmarkers count
$post->bookmarkers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('bookmarks')->get();

foreach($users as $user){
    echo $user->bookmarks_count;
}
```

## N +1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the with method:

```php
// Bookmarker
$users = App\User::with('bookmarks')->get();

foreach($users as $user) {
    $user->hasBookmarked($post);
}

// Bookmarkable
$posts = App\Post::with('bookmarks')->get();
// or
$posts = App\Post::with('bookmarkers')->get();

foreach($posts as $post){
    $post->isBookmarkedBy($user);
}
```

## Events

|Event|Description|
|---|---|
|`Trendsoft\LaravelBookmark\Events\Bookmarked`|Triggered when the relationship is created.|
|`Trendsoft\LaravelBookmark\Events\Unbookmarked`|Triggered when the relationship is deleted.|

## Contributing

You can contribute in one of three ways:

- File bug reports using the [issue tracker](https://github.com/trendsoft/laravel-bookmark/issues).
- Answer questions or fix bugs on the [issue tracker](https://github.com/trendsoft/laravel-bookmark/issues).
- Contribute new features or update the wiki.

The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable.

# License

MIT