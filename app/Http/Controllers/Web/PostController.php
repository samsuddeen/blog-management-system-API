<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:post-create', ['only' => ['store']]);
        $this->middleware('permission:post-edit', ['only' => ['update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
    }
   public function index()
    {
        $posts = Post::with('category')->latest()->get();
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.posts.form',compact('categories'));
    }

    public function store(PostRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($request->title);
            $data['author_id'] = auth()->id();
            Post::create($data);
            DB::commit();
            return redirect()->route('post.index')->with('success', 'post created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    public function show(Post $post)
    {
        $categories = Category::where('status', true)->get();

        return view('admin.posts.show', compact('post','categories'));
    }


    public function edit(Post $post)
    {
        $categories = Category::where('status', true)->get();

        return view('admin.posts.form', compact('post','categories'));
    }

    public function update(PostRequest $request, Post $post)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['slug'] = Str::slug($request->title);
           $post->update($data);
            DB::commit();
            return redirect()->route('post.index')->with('success', 'post updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        DB::beginTransaction();
        try {
            $post->delete();
            DB::commit();
            return redirect()->route('post.index')->with('success', 'post deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }


    public function forceDelete($id)
{
    DB::beginTransaction();
    try {
        $post = Post::withTrashed()->findOrFail($id);

        $post->forceDelete();

        DB::commit();
        return redirect()->back()->with('success', 'post permanently deleted');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

public function restore($id)
{
    DB::beginTransaction();
    try {
        $post = Post::withTrashed()->find($id);
        if ($post) {
            $post->restore();
        }

        DB::commit();
        return redirect()->route('post.index')->with('success', 'post restored successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

    public function changeStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $post = Post::findOrFail($id);
            $post->status = $request->status;
            $post->save();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Successfully updated'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $th->getMessage()], 500);
        }
    }
}
