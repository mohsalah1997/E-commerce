<?php

namespace App\Models;

use App\Scopes\QuantityScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable=[
        'name','description','category_id','price',
        'quantity','image'
    ];


    public function category(){
       return $this->belongsTo(Category::class,'category_id','id');
    }
    public function tags(){
        return $this->belongsToMany(Tag::class,'products_tags'
//            ,'product_id',
//            'tag_id','id','id'
        );
    }

    public function desc(){
        return $this->hasOne(ProductDescription::class,'product_id','id')->withDefault();

    }
    public function scopeWithImages($query){
        return $query->whereNotNull('image');
    }

    public function scopePrice(Builder $query,$price1,$price2){
        return $query->whereBetween('price',[$price1,$price2]);

    }
    public function scopeFilter(Builder $query,$filters=[]){

        $defaults=[
            'name'=>null,
            'category_id'=>null
        ];
        $filters=array_merge($defaults,$filters);


       return $query->when($filters['name'],function (Builder $query,$name){
            $query->where('name','LIKE',"%$name%");
        })->when($filters['category_id'],function (Builder $query,$category_id){
            $query->where('category_id',$category_id);
        });


    }

    protected static function booted()
    {
//        self::addGlobalScope('quantity',function (Builder $query){
//            $query->where('quantity','>',0);
//        });

        self::addGlobalScope(new QuantityScope());

        self::forceDeleted(function ($product){
            Storage::disk('public')->delete($product->image);


        });
    }
}
