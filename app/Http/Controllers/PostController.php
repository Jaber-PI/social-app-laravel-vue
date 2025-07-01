<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        $allFilesPaths = [];

        try {
            $attachments = $data['attachments'];
            unset($data['attachments']);
            $post = Auth::user()->posts()->create($data);
            $allFilesPaths = $post->addAttachments($attachments);
            DB::commit();
            return redirect()->back()->with('success', 'post created');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($allFilesPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            // throw $e;
            return redirect()->back()->with('error', 'post not created');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
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
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $attachments = $data['attachments'];
            $deleted_attachments = $data['deleted_attachments'];
            unset($data['attachments'], $data['deleted_attachments']);
            $post->update($data);
            if ($attachments) {
                $post->addAttachments($attachments);
            }
            foreach ($deleted_attachments as $attachment) {
                PostAttachment::destroy($attachment);
            }
            DB::commit();
            return redirect()->back()->with('success', 'post updated');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return redirect()->back()->with('error', 'post not updatedd');
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->created_by) {
            abort(403, 'Permission denied.');
        }

        $post->delete();
    }


    public function downloadAttachment(PostAttachment $attachment)
    {
        return Storage::disk('public')->download($attachment->file_path, $attachment->filename);
    }
}
