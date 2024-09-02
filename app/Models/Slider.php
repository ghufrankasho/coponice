<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    public $table = 'sliders';
     protected $hidden=['created_at','updated_at'];
    protected $fillable=['link','type','sorting','alt'];
}