<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\Product\Database\Factories\CategoryFactory;

class Category extends Model
{
 
    use HasFactory, HasTranslations;

    protected $fillable = ['name', 'parent_id'];
    public $translatable = ['name'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // protected static function newFactory(): CategoryFactory
    // {
    //     // return CategoryFactory::new();
    // }
}
