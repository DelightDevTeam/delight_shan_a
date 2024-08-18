<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Admin\GameType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameList extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name', 'click_count', 'game_type_id', 'product_id', 'image_url', 'status', 'hot_status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }

    public function getImgUrlAttribute()
    {
        return asset('/game_logo/'.$this->image);
    }
}
