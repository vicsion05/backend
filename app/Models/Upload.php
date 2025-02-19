<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'file_path', 'file_type', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}