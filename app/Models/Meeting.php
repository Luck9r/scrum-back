<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'datetime',
        'meeting_link',
        'repeat',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
