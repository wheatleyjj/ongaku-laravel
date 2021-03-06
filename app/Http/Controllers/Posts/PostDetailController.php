<?php
/**
 * Created by PhpStorm.
 * User: wheatleyjj
 * Date: 13/01/2018
 * Time: 15:58
 */

namespace App\Http\Controllers\Posts;


use App\Http\Controllers\APIInterface;
use App\Http\Requests\StoreComment;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostDetailController extends APIInterface
{
    public function delete(Post $post) {
        if (Auth::id() != $post->created_by_id) {
            return $this->APIResponse(false, "UNAUTHORISED", null, 401);
        }

        $post->delete();
        return $this->APIResponse(true);
    }

    public function createComment(Post $post, StoreComment $request) {
        $comment = $post->comments()->save(new Comment(array_merge($request->all(), [
            'created_by_id' => Auth::id(),
            'post_id' => $post->id
        ])));

        return $this->APIResponse(true, null, $comment->id, 201);
    }

    public function like(Post $post) {
        $existingLike = Like::where('likeable_id', $post->id)->where('likeable_type', 'App\\Models\\Post')->where('created_by_id', Auth::id())->first();

        if (isset($existingLike)) {
            $existingLike->delete();
            return $this->APIResponse(true);
        }

        $like = $post->likes()->create(['created_by_id' => Auth::id()]);
        return $this->APIResponse(true);
    }
}