<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Requests\PostsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostsController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Post::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $order_by = $request->input('order_by', 'created_at');
        $order = $request->input('order', 'desc');
        $per_page = $request->input('per_page', config('defaults.per_page'));

        $posts = Post::orderBy($order_by, $order);

        if ($search = $request->input('search')) {
            $posts->search($search);
        }

        if ($category = $request->input('category')) {
            $posts->where('category_id', $category);
        }

        if ($tag = $request->input('tag')) {
            $posts->whereHasTag($tag);
        }

        $posts = $posts->with('category', 'tags')
                       ->paginate($per_page)
                       ->appends($request->except('page'));

        return view('admin.posts.index', compact('posts'));
    }

    public function create(Request $request)
    {
        return view('admin.posts.create', [
            'post' => new Post(),
        ]);
    }

    public function store(PostsRequest $request)
    {
        $post = new Post($request->validated());
        $post->category()->associate($request->input('category'));
        $post->save();

        if ($tags = $request->input('tags', [])) {
            $post->tags()->sync($tags);
        }

        $this->flashSuccessMessage();
        return to_route('admin.posts.edit', $post);
    }

    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(PostsRequest $request, Post $post)
    {
        $post->fill($request->validated());

        if ($category = $request->input('category')) {
            $post->category()->associate($category);
        }

        $post->save();

        if ($tags = $request->input('tags', [])) {
            $post->tags()->sync($tags);
        }

        $this->flashSuccessMessage();
        return to_route('admin.posts.edit', $post);
    }

    public function destroy(Post $post, Request $request)
    {
        if (! $post->delete()) {
            if ($request->expectsJson()) {
                return response()->json(false, 500);
            }

            abort(500);
        }

        if ($request->expectsJson()) {
            return response()->json(true);
        }

        return to_route('admin.posts.index');
    }

    public function bulk(Request $request)
    {
        $this->authorize('viewAny', Post::class);

        $this->validate($request, [
            'action' => 'required|in:delete',
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id',
        ]);

        $action = $request->input('action');
        $ids = $request->input('posts', []);

        switch ($action) {
            case 'delete':

                // make sure allowed to delete
                $this->authorize('delete_posts');

                Post::whereIn('id', $ids)
                               ->get()
                               ->each(function (Post $post) {
                                   $post->delete();
                               });
                break;
        }

        $this->flashSuccessMessage();
        return to_route('admin.posts.index');
    }
}
