<?php

use Illuminate\Support\Facades\Route;

// Blog Posts...
Route::get('/api/posts', 'PostsController@index')->name('posts.index');
Route::get('/api/posts/{id?}', 'PostsController@show')->name('posts.show');
Route::post('/api/posts/{id}', 'PostsController@store')->name('posts.store');
Route::delete('/api/posts/{id}', 'PostsController@delete')->name('posts.delete');

// Blog Tags...
Route::get('/api/tags', 'TagsController@index')->name('tags.index');
Route::get('/api/tags/{id?}', 'TagsController@show')->name('tags.show');
Route::post('/api/tags/{id}', 'TagsController@store')->name('tags.store');
Route::delete('/api/tags/{id}', 'TagsController@delete')->name('tags.delete');

// Blog Authors...
Route::get('/api/team', 'TeamController@index')->name('team.index');
Route::get('/api/team/{id?}', 'TeamController@show')->name('team.show');
Route::post('/api/team/{id}', 'TeamController@store')->name('team.store');
Route::delete('/api/team/{id}', 'TeamController@delete')->name('team.delete');

// Blog Image Uploads
Route::post('/api/uploads', 'ImageUploadsController@upload')->name('images.store');

// Blog Pages...
Route::get('/api/pages', 'PagesController@index')->name('pages.index');
Route::get('/api/pages/{id?}', 'PagesController@show')->name('pages.show');
Route::post('/api/pages/{id}', 'PagesController@store')->name('pages.store');
Route::delete('/api/pages/{id}', 'PagesController@delete')->name('pages.delete');

// Categories...
Route::get('/api/categories', 'CategoriesController@index')->name('categories.index');
Route::get('/api/categories/{id?}', 'CategoriesController@show')->name('categories.show');
Route::post('/api/categories/{id}', 'CategoriesController@store')->name('categories.store');
Route::delete('/api/categories/{id}', 'CategoriesController@delete')->name('categories.delete');

// Logout Route...
Route::get('/logout', 'LoginController@logout')->name('logout');

// Catch-all Route...
Route::get('/{view?}', 'SPAViewController')->name('spa')->where('view', '(.*)');
