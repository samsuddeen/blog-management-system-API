<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\PublicPostResource;
use Throwable;

class PublicPostController extends Controller
{

    public function index()
{
    try {
        $posts = Post::with(['category', 'author'])->latest()->get();

        $response = [
            'error' => false,
            'message' => 'Public posts fetched successfully',
            'data' => PublicPostResource::collection($posts)
        ];

        return response()->json($response, 200);

    } catch (Throwable $e) {
        $response = [
            'error' => true,
            'message' => 'Failed to fetch public posts',
            'exception' => $e->getMessage()
        ];

        return response()->json($response, 500);
    }
}

}
