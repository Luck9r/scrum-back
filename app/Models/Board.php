<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['title'];


    public static function boot()
    {
        parent::boot();

        static::created(function ($board) {
            // Create default statuses with order
            $statuses = ['To Do', 'In Progress', 'QA', 'Done'];
            foreach ($statuses as $index => $statusName) {
                $status = Status::create(['name' => $statusName]);
                $board->statuses()->attach($status->id, ['order' => $index + 1]);
            }

            // Assign the creator to the board
            $user = Auth::user();
            $board->users()->attach($user->id);
        });
    }
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

    public function priorities()
    {
        // for now priorities are common for all boards
        return Priority::all();
    }
}
