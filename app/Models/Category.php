<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public function advert(){
        return $this->hasMany(Advert::class);
    }
   protected $hidden=['created_at','updated_at'];
   protected $fillable = ['name','seo_title','seo_description','seo_keywords','seo_image'];
}