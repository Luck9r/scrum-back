<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
//    use HasFactory;

    protected $fillable = ['title', 'content', 'status_id', 'due_date', 'priority_id', 'board_id', 'assignee_id', 'creator_id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->slug = $task->generateSlug();
        });
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generateSlug(): string
    {
        $board = $this->board;
        $boardNameInitials = collect(explode(' ', $board->title))->map(function ($word) {
            return strtoupper($word[0]);
        })->join('');

        $taskCount = Task::where('board_id', $this->board_id)->count() + 1;

        return $boardNameInitials . '-' . $taskCount;
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
