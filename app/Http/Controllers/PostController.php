<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Post;
use App\Models\Follow;

class PostController extends Controller
{
    public function getAllPosts(Request $request)
    {
        try {
            $userid = $request->route('userid');
            
            $followed = Follow::select('followed')
            ->where('following', $userid)
            ->get();

            $posts = Post::with(['user' => function ($query) {
                $query->select('userid','username', 'profilepicture');
            }])
            ->where(function ($query) use ($followed, $userid) {
                $query->whereIn('userid', $followed)
                      ->orWhere('userid', $userid);
            })
            ->select('postid', 'userid', 'datetime', 'content', 'postpicture', 'likes', 'comments')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'All posts.',
                'posts' => $posts
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function getAllUserPosts(Request $request)
    {
        try {
            $userid = $request->route('userid');

            $posts = Post::where('userid', $userid)
            ->select('postid', 'userid', 'datetime', 'content', 'postpicture', 'likes', 'comments')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'All user posts.',
                'posts' => $posts], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }

    public function createPost(Request $request)
    {
        try {
            $userid = Auth::user()->userid;
            $content = $request->input('content');
            $postpicture = $request->input('postpicture');

            if (!$content && !$postpicture) {
                return response()->json([
                    'status' => false,
                    'messsage' => 'Post and/or Post Picture can\'t be empty.'], 400);
            }

            $create = Post::create([
                'userid' => $userid,
                'datetime' => date('Y-m-d H:i:s'),
                'content' => $content,
                'postpicture'=> $postpicture,
                'likes' => 0,
                'comments' => 0
            ]);

            $newpostid = $create->postid;

            if (!$create) {
                return response()->json([
                    'status' => false,
                    'messsage' => 'Can\'t create post.'
                ], 400);
            }

            return response()->json([
                'status' => true,
                'messsage' => 'Post has been created',
                'postid' => $newpostid
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }
    public function postDetails(Request $request)
    {
        try {
            $postid = $request->route('postid');

            $details = Post::with(['user' => function ($query) {
                $query->select('userid','username', 'profilepicture');
            }])
            ->where('postid', $postid)
            ->select('postid', 'userid', 'datetime', 'content', 'postpicture', 'likes', 'comments')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'Post details.',
                'details' => $details
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }
    public function editPost(Request $request)
    {
        try {
            $postid = $request->route('postid');
            $userid = Auth::user()->userid;
            $content = $request->input('content');
            $postpicture = $request->input('postpicture');

            if (!$content && !$postpicture) {
                return response()->json([
                    'status' => false,
                    'messsage' => 'Post and/or Post Picture can\'t be empty.'], 400);
            }

            $update = Post::where('postid', $postid)
            ->where('userid', $userid)
            ->update([
                'datetime' => date('Y-m-d H:i:s'),
                'content' => $content,
                'postpicture' => $postpicture,
            ]);

            if (!$update) {
                return response()->json([
                    'status' => false,
                    'messsage' => 'Post is not existed or this is not your post.'], 400);
            }

            return response()->json([
                'status' => true,
                'messsage' => 'Post has been updated.'
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }
    public function deletePost(Request $request)
    {
        try {
            $postid = $request->route('postid');
            $userid = Auth::user()->userid;

            $delete = Post::where([
                'postid' => $postid,
                'userid' => $userid
            ])->delete();

            if(!$delete){
                return response()->json([
                    'status' => false,
                    'messsage' => 'Post is not existed or this is not your post.'], 400);
            }

            return response()->json([
                'status' => true,
                'messege' => 'your post has been deleted'
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.'
            ], 500);
        }
    }
}