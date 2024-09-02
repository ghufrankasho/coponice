<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;
    public function category(){
        return $this->belongsTo(Category::class);
    }
    protected $fillable = [
        'name', 'link', 'type','title','main','description','counter','discount','code'
    ,'visible','expire_date','short_description',
    'seo_title','seo_description','seo_keywords','seo_image'
    ];
}