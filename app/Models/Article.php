<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Article extends Model
{
    protected $fillable = [

        'category_id',

        'title',

        'slug',

        'image',

        'short_description',

        'description',

        'status',

        'featured',

        'views'

        ];

        public function category()
        {
            return $this->belongsTo(Category::class);
        }

        public function tags()
        {
            return $this->belongsToMany(Tag::class);
        }
}
