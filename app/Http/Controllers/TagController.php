<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function create(Request $request)
{
    $request->validate([
        'name' => 'required|unique:tags,name',
        'status' => 'nullable'
    ]);

    $tag = new Tag();

    $tag->name = $request->name;
    $tag->slug = Str::slug($request->name);
    $tag->status = $request->status ?? 1;

    $tag->save();

    return response()->json([
        'status' => true,
        'message' => 'Tag Created Successfully.',
        'data' => $tag
    ], 201);
}

public function index()
{
    $tags = Tag::latest()->get();

    return response()->json([
        'status' => true,
        'data' => $tags
    ]);
}

public function edit($id)
{
    $tag = Tag::find($id);

    if (!$tag) {
        return response()->json([
            'status' => false,
            'message' => 'Tag not found.'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $tag
    ]);
}

public function update(Request $request, $id)
{
    $tag = Tag::find($id);

    if (!$tag) {
        return response()->json([
            'status' => false,
            'message' => 'Tag not found.'
        ], 404);
    }

    $request->validate([
        'name' => 'required|unique:tags,name,' . $id,
        'status' => 'nullable'
    ]);

    $tag->name = $request->name;
    $tag->slug = Str::slug($request->name);
    $tag->status = $request->status ?? 1;

    $tag->save();

    return response()->json([
        'status' => true,
        'message' => 'Tag Updated Successfully.',
        'data' => $tag
    ]);
}

public function destroy($id)
{
    $tag = Tag::find($id);

    if (!$tag) {
        return response()->json([
            'status' => false,
            'message' => 'Tag not found.'
        ], 404);
    }

    $tag->delete();

    return response()->json([
        'status' => true,
        'message' => 'Tag Deleted Successfully.'
    ]);
}
}
