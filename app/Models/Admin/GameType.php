<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'order', 'img'];

    protected $appends = ['image', 'img_url'];

    public function getImgUrlAttribute()
    {
        return asset('assets/img/game_type/'.$this->img);
    }
}
