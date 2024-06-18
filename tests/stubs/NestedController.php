<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;

class CategoryPostsController extends Controller
{
    use HasOrdersBy;

    protected static function initOrderbys()
    {
        static::$orderbys = [
            'id'         => __('Id'),
            'created_at' => __('Created At'),
            'updated_at' => __('Updated At'),
        ];
    }

    public function index(Category $category, Request $request)
    {
        $this->authorize('viewAny', [Post::class, $category]);
        $title = __('All Posts');
        $order_by = $this->getOrderBy($request, 'created_at');
        $order = $this->getOrder($request, 'created_at', $order_by);
        $per_page = $this->getPerPage($request);

        $posts = $category->posts()->orderBy($order_by, $order);

        $search = null;
        if ($search = $request->input('search')) {
            $posts->search($search);
            $title = __('Posts matching \:search\'', ['search' => $search]);
        }

        if ($date_field = $request->input('date_field')) {
            $posts->dateBetween($date_field, $request->input('date_from'));
        }

        $posts = $posts->with('category')
                       ->paginate($per_page)
                       ->append($request->except('page'));

        return view('admin.categories.posts.index', compact('posts'));
    }

    public function create(Request $request, Category $category)
    {
        $this->authorize('create', [Category::class, $category]);
        return view('admin.categories.posts.create', compact('category'));
    }

    public function store(Category $category, CategoryPostRequest $request)
    {
        $this->authorize('create' . [Post::class, $category]);
        $post = new Post($request->validated());
        $post->category()->associate($category);
        $post->save();

        $this->flashSuccessMessage();

        return to_route('admin.categories.posts.edit', compact('category', 'post'));
    }

    public function show(Category $category, Post $post)
    {
        $this->authorize('view', [$post, $category]);
        return view('admin.categories.posts.show', compact('post', 'category'));
    }

    public function edit(Category $category, Post $post)
    {
        $this->authorize('update', [$post, $category]);

        return view('admin.categories.posts.edit', compact('category', 'post'));
    }

    public function update(CategoryPostRequest $request, Category $category, Post $post)
    {
        $this->authorize('update', [$post, $category]);
        $post->fill($request->validated());
        if ($request->has('category')) {
            $post->category()->associate($category);
        }
        $post->save();

        $this->flashSuccessMessage();

        return to_route('admin.categories.posts.index', compact('category', 'post'));
    }

    public function destroy(Request $request, Category $category, Post $post)
    {
        $this->authorize('delete', [$post, $category]);

        if (! $post->delete()) {
            if ($request->expectsJson()) {
                return response()->json(false, 500);
            }

            abort(500);
        }

        if ($request->expectsJson()) {
            return response()->json(true);
        }

        return to_route('admin.categories.posts.index', compact('category'));
    }

    public function bulk(Category $category, Request $request)
    {
        $this->authorize('viewAny', [Post::class, $category]);

        $this->validate($request, [
            'action'  => ['required', Rule::in('delete')],
            'posts'   => ['required', 'array'],
            'posts.*' => [Rule::exists('posts', 'id')],
        ]);

        $action = $request->input('action');
        $ids = $request->input('posts', []);

        switch($action) {
            case 'delete':
                $this->authorize('delete posts');
                Post::whereIn('id', $ids)
                    ->get()
                    ->each(fn (Post $post) => $post->delete());
                break;
        }

        $this->flashSuccessMessage();

        return $this->redirect($request, action([CategoryPostsController::class, 'index']));
    }


}
