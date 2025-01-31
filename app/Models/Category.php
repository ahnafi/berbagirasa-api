<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $keyType = "string";
    protected $primaryKey = "id";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        "id",
        "name"
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
