<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UrlController extends Controller
{
    public function single(Request $request): JsonResponse
    {
        $start = microtime(true);

        // check if path is provided
        $url = DB::table('urls')
            ->where('path', '=', $request->input('path'))
            ->first();

        // return error if path not found
        if (!$url) {
            return response()->json([
                'time' => microtime(true) - $start,
                'url' => $url,
            ]);
        }

        $data = [];

        // get data based on content type
        switch ($url->content_type) {
            // HOME PAGE DATA
            case 'INDEX': {
                $posts = DB::table('posts')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                $data = [
                    'posts' => $posts,
                ];
                break;
            }
            // BLOG POST DATA
            case 'POST': {
                $post = DB::table('posts')
                    ->where('id', '=', $url->content_id)
                    ->first();
                $data = [
                    'post' => $post,
                ];
                break;
            }
            // AUTHORS INDEX PAGE DATA
            case 'AUTHORS_INDEX': {
                $authors = DB::table('authors')
                    ->orderBy('name', 'asc')
                    ->limit(10)
                    ->get();
                $data = [
                    'authors' => $authors,
                ];
                break;
            }
            case 'AUTHOR': {
                $author = DB::table('authors')
                    ->where('id', '=', $url->content_id)
                    ->first();
                $data = [
                    'author' => $author,
                ];
                break;
            }
        }

        return response()->json([
            'time' => microtime(true) - $start,
            'url' => $url,
            ...$data,
        ]);
    }
}