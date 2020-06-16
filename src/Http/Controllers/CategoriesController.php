<?php

namespace Wink\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Wink\Http\Resources\CategoriesResource;
use Wink\WinkCategory;

class CategoriesController
{
    /**
     * Return posts.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $entries = WinkCategory::when(request()->has('search'), function ($q) {
            $q->where('name', 'LIKE', '%'.request('search').'%');
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return CategoriesResource::collection($entries);
    }

    /**
     * Return a single post.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id = null)
    {
        if ($id === 'new') {
            return response()->json([
                'entry' => WinkCategory::make([
                    'id' => Str::uuid(),
                ]),
            ]);
        }

        $entry = WinkCategory::findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Store a single category.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $data = [
            'name' => request('name'),
            'slug' => request('slug'),
        ];

        validator($data, [
            'name' => 'required',
            'slug' => 'required|'.Rule::unique(config('wink.database_connection').'.wink_categories', 'slug')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? WinkCategory::findOrFail($id) : new WinkCategory(['id' => request('id')]);

        $entry->fill($data);

        $entry->save();

        return response()->json([
            'entry' => $entry->fresh(),
        ]);
    }

    /**
     * Return a single category.
     *
     * @param  string  $id
     * @return void
     */
    public function delete($id)
    {
        $entry = WinkCategory::findOrFail($id);

        $entry->delete();
    }
}
