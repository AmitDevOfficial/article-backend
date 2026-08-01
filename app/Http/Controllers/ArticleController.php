<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\Category;

class ArticleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|unique:articles,title',
            'short_description' => 'nullable',
            'description' => 'required',
            'status' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destination = public_path('uploads/articles');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $image->move($destination, $filename);
        } else {
            $filename = "default.png";
        }

        $article = new Article();
        $article->category_id = $request->category_id ?: null;
        $article->title = $request->title;
        $article->slug = Str::slug($request->title);
        $article->image = $filename;
        $article->short_description = $request->short_description;
        $article->description = $request->description;
        $article->status = $request->status ?: 1;
        $article->featured = $request->featured ?? 0;
        $article->views = 0;
        $article->save();

        if ($request->filled('tags')) {
            $article->tags()->sync($request->tags);
        }

        return response()->json([
            'status' => true,
            'message' => 'Article Published Successfully.',
            'data' => $article
        ], 201);
    }

    public function trending()
    {
        $articles = Article::with(['tags','category'])
        ->whereHas('tags', function ($q) {
            $q->where('slug', 'article');
        })
        ->latest()
        ->take(3)
        ->get();

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function latestNews()
{
    $articles = Article::with(['category', 'tags'])
        ->where('status', 1)
        ->where(function ($query) {

            // News Category
            $query->whereHas('category', function ($q) {
                $q->where('slug', 'news');
            })

            // OR News Tag
            ->orWhereHas('tags', function ($q) {
                $q->where('slug', 'news');
            });

        })
        ->latest()
        ->take(4)
        ->get();

    return response()->json([
        'status' => true,
        'data' => $articles
    ]);
}

public function featuredNews()
{
    $article = Article::with(['category', 'tags'])
        ->where('status', 1)
        ->where('featured', 1)
        ->latest()
        ->first();

    return response()->json([
        'status' => true,
        'data' => $article
    ]);
}

public function articles()
{
    $articles = Article::with(['category', 'tags'])
        ->where('status', 1)
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'data' => $articles,
    ]);
}

public function categoryPage()
{
    $categories = Category::with(['articles' => function ($q) {
        $q->latest()->take(3);
    }])->get();

    return response()->json([
        'data' => $categories
    ]);
}

public function categoryArticles($slug)
{
    $category = Category::where('slug', $slug)->firstOrFail();

    $articles = Article::with(['category', 'tags'])
        ->where('category_id', $category->id)
        ->where('status', 1)
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'category' => $category,
        'articles' => $articles
    ]);
}

    // 👇 View Articles (list)
    public function index()
        {
            $articles = Article::with('tags')->latest()->get();

            return response()->json([
                'status' => true,
                'data' => $articles
            ]);
        }

    // 👇 Fetch single article (edit form ke liye)
    public function edit($id)
{
    $article = Article::with('tags')->find($id);

    if (!$article) {
        return response()->json([
            'status' => false,
            'message' => 'Article not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $article
    ]);
}

    // 👇 Update article
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|unique:articles,title,' . $id,
            'short_description' => 'nullable',
            'description' => 'required',
            'status' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('image')) {
            // purani image delete karo (agar default nahi hai)
            $oldPath = public_path('uploads/articles/' . $article->image);
            if ($article->image && $article->image !== 'default.png' && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destination = public_path('uploads/articles');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $image->move($destination, $filename);
            $article->image = $filename;
        }

        $article->category_id = $request->category_id ?: null;
        $article->title = $request->title;
        $article->slug = Str::slug($request->title);
        $article->short_description = $request->short_description;
        $article->description = $request->description;
        $article->status = $request->status ?: 1;
        $article->featured = $request->featured ?? 0;
        $article->save();

        if ($request->filled('tags')) {
            $article->tags()->sync($request->tags);
        } else {
            $article->tags()->detach();
        }

        return response()->json([
            'status' => true,
            'message' => 'Article Updated Successfully.',
            'data' => $article
        ]);
    }

    // 👇 Delete article
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $path = public_path('uploads/articles/' . $article->image);
        if ($article->image && $article->image !== 'default.png' && file_exists($path)) {
            unlink($path);
        }

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'Article Deleted Successfully.'
        ]);
    }

    public function uploadImage(Request $request)
{
    $request->validate([
        'upload' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
    ]);

    $image = $request->file('upload');

    $filename = time().'_'.$image->getClientOriginalName();

    $destination = public_path('uploads/articles');

    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    $image->move($destination, $filename);

    return response()->json([
        "url" => asset("uploads/articles/".$filename)
    ]);
}

    public function singleArticle($slug)
{
    $article = Article::with(['category','tags'])
        ->where('slug',$slug)
        ->where('status',1)
        ->firstOrFail();

    $article->increment('views');

    $related = Article::with('category')
        ->where('status',1)
        ->where('category_id',$article->category_id)
        ->where('id','!=',$article->id)
        ->latest()
        ->take(4)
        ->get();

    return response()->json([
        'status'=>true,
        'article'=>$article,
        'related'=>$related
    ]);
}
}