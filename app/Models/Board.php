<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
//    use HasFactory;

    protected $fillable = ['title'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'board_user');
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'board_status');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
