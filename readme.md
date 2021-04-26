<p align="center"><img src="/art/header.png?1" alt="wink logo"></p>

Wink adds a nice UI where you can manage a publication of any size with posts, pages, tags, and authors.

You can add photos, code blocks, featured images, social media & SEO attributes, embedded HTML (YouTube Videos, Embedded Podcasts Episodes, Tweets, ...), and markdown!

Wink is used to manage the [official Laravel blog](https://blog.laravel.com), [divinglaravel.com](https://divinglaravel.com), and many more.

Dark & Light modes available so everyone is happy 😁

## Installation

Wink uses a separate database connection and authentication system so that you don't have to modify any of your project code.

To install Wink, run these commands in the root of your Laravel app:

```sh
composer require themsaid/wink
php artisan wink:install
php artisan storage:link
```

**Configure the database connection** wink is going to be using in `config/wink.php`. Then run:

```sh
php artisan wink:migrate
```

Head to `yourproject.test/wink` and use the provided email and password to log in.

## Uploading to S3

If you want to upload images to S3, update the `storage_disk` attribute in your `wink.php` configuration file to s3. Make sure your S3 disk is correctly configured in your `filesystems.php` configuration file.

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('CDN_URL'),
    'options' => [
        'CacheControl' => 'public, max-age=315360000'
    ],
],
```

Note: you're going to need to install the AWS-S3 Flysystem adapter, using `composer require league/flysystem-aws-s3-v3` for this to work.

## Using Unsplash

Visit https://unsplash.com/oauth/applications to create a new unsplash app. Grab the 'Access Key' and add it to your `.env` file as `UNSPLASH_ACCESS_KEY`. Lastly, add unsplash to your `config/services.php` file:

```php
'unsplash' => [
    'key' => env('UNSPLASH_ACCESS_KEY'),
],
```

## Updates

After each update, make sure you run these commands:

```sh
php artisan wink:migrate
php artisan vendor:publish --tag=wink-assets --force
```

## Displaying your content

Wink is faceless, it doesn't have any opinions on how you display your content in your frontend. You can use the wink models in your controllers to display the different resources:

- `Wink\WinkPost`
- `Wink\WinkPage`
- `Wink\WinkAuthor`
- `Wink\WinkTag`

To display posts and pages content, use `$post->content` instead of `$post->body`. The content will always be in HTML format while the body might be HTML or raw markdown based on the post type.

## Generating a sample controller

You can **generate a sample controller** by running:

```sh
php artisan wink:controller BlogController
```

This will create a file in your `Http/Controllers` directory named `BlogController.php`.  You can replace `BlogController` in the commandnoun you prefer which will prefix the file and class names  (e.g. if you use`php artisan wink:controller Post` the file and class created will be named `PostController` with any name for your controller. 

The sample controller contains the following code:

```angular2
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Wink\WinkPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = WinkPost::with('tags')
            ->live()
            ->orderBy('publish_date', 'DESC')
            ->simplePaginate(10);
        return view('blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show ($slug)
    {
        $post = WinkPost::live()->whereSlug($slug)->firstOrFail();

        return view('post.index', [
            'post' => $post
        ]);
    }
}
```
 
 This mean the `index()` method will retrieve 10 published blog posts per page with their associated tags, most recent first, and render them in the `blog.index` view. You will need to create that view and the associated route.
 
 The `show()` method in turn will retrieve a single published post corresponding to its slug and render it in the `post.index` view,  displaying an error message if it's not found.  You will need to create that view with the associated route,. 
 
## Credits

- [Mohamed Said](https://github.com/themsaid)
- [All contributors](https://github.com/themsaid/wink/contributors)

Special thanks to [Caneco](https://twitter.com/caneco) for the logo ✨

## Contributing

Check the [contribution guide](CONTRIBUTING.md).

## License

Wink is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
