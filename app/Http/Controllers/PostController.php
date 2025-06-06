<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Exports\PostsExport;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:post-create', ['only' => ['store']]);
        $this->middleware('permission:post-edit', ['only' => ['update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
   public function index()
{
    try {

        $posts = Post::with(['category', 'author'])->get();

        $response = [
            'error' =>false,
            'message' => 'Posts fetched successfully',
            'data' => PostResource::collection($posts)
        ];

        return response()->json($response, 200);

    } catch (\Exception $e) {
        $response = [
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(PostRequest $request)
{
    try {

        if (!Auth::user()->hasPermissionTo('post-create')) {
                return response()->json([
                    'error' => true,
                    'message' => 'You do not have permission to create a post.'
                ], 403);
            }

        $post = Post::create(array_merge(
            $request->validated(),
            ['author_id' => Auth::id()]
        ));



        $response = [
            'error' => false,
            'message' => 'Post created successfully',
            'data' => new PostResource($post)
        ];

        return response()->json($response, 201);

    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to create post',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}



    /**
     * Display the specified resource.
     */
   public function show(Post $post)
{
    try {
        $post->load(['category', 'author']);

        $response = [
            'error' => false,
            'message' => 'Post fetched successfully',
            'data' => new PostResource($post)
        ];

        return response()->json($response, 200);

    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to fetch post',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
{
    try {
        if ($post->author_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->update($request->validated());

        $response = [
            'error' => false,
            'message' => 'Post updated successfully',
            'data' => new PostResource($post)
        ];

        return response()->json($response, 200);

    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to update post',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
{
    try {
        if ($post->author_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'error' => true,
                'message' => 'Unauthorized'
            ], 403);
        }

        $post->delete();

        $response = [
            'error' => false,
            'message' => 'Post deleted successfully'
        ];

        return response()->json($response, 200);

    } catch (\Exception $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to delete post',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}


//     public function export()
// {
//     try {
//         return Excel::download(new PostsExport, 'posts.xlsx');

//     } catch (\Exception $e) {
//         $response = [
//             'error' => true,
//             'message' => 'Failed to export posts',
//             'exception' => $e->getMessage()
//         ];

//         return response()->json($response, 500);
//     }
// }

public function export()
{
    try {
        $fileName = 'posts_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PostsExport, $fileName, ExcelFormat::XLSX);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Failed to export posts',
            'exception' => $e->getMessage()
        ], 500);
    }
}
}
