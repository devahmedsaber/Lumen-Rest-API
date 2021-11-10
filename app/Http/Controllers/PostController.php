<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiTrait;

    public function index()
    {
        $posts = Post::all();
        return $this->success('Posts Fetched Successfully.', $posts);
    }

    public function store(Request $request)
    {
        try {
            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            if ($post)
                return $this->success('Post Created Successfully.', $post);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            if ($post)
                return $this->success('Post Updated Successfully.', $post);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->delete();

            if ($post)
                return $this->success('Post Deleted Successfully.', $post);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
