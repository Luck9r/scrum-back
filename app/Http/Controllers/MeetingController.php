<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Meeting;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MeetingController extends Controller
{
    public function index()
    {
        return Meeting::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'datetime' => 'required|date',
            'meeting_link' => 'required|string',
            'repeat' => 'nullable|string',
        ]);

        $meeting = Meeting::query()->create($request->all());

        return response()->json($meeting, 201);
    }

    public function show(Meeting $meeting)
    {
        return $meeting;
    }

    public function update(Request $request, Meeting $meeting)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'datetime' => 'sometimes|required|date',
            'meeting_link' => 'sometimes|required|string',
            'repeat' => 'nullable|string',
        ]);

        $meeting->update($request->all());

        return response()->json($meeting, 200);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return response()->json(null, 204);
    }

    public function storeForBoard(Request $request, $boardId)
    {
        $board = Board::query()->findOrFail($boardId);

        $request->validate([
            'name' => 'required|string',
            'datetime' => 'required|date',
            'meeting_link' => 'required|string',
            'repeat' => 'nullable|string',
        ]);

        $meeting = new Meeting($request->all());
        $board->meetings()->save($meeting);

        $meeting->refresh();

        $users = $board->users;
        $frontendUrl = config('app.frontend_url');
        $calendarUrl = "{$frontendUrl}/board/{$meeting->boardId}/calendar";

        foreach ($users as $user) {
            Mail::raw("A new meeting '{$meeting->name}' has been scheduled for {$meeting->datetime}, set to repeat {$meeting->repeat}. You can view the meeting details here: {$calendarUrl}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('New Meeting Scheduled');
            });
        }

        return response()->json($meeting, 201);
    }

    public function updateForBoard(Request $request, $boardId, $meetingId)
    {
        $board = Board::query()->findOrFail($boardId);
        $meeting = $board->meetings()->findOrFail($meetingId);

        $request->validate([
            'name' => 'sometimes|required|string',
            'datetime' => 'sometimes|required|date',
            'meeting_link' => 'sometimes|required|string',
            'repeat' => 'nullable|string',
        ]);

        $meeting->update($request->all());

        return response()->json($meeting, 200);
    }


    public function destroyForBoard($boardId, $meetingId)
    {
        $board = Board::query()->findOrFail($boardId);
        $meeting = $board->meetings()->findOrFail($meetingId);

        $meeting->delete();

        return response()->json(null, 204);
    }

    public function getMeetingsByBoard($boardId)
    {
        $board = Board::query()->findOrFail($boardId);
        $meetings = $board->meetings;

        return response()->json($meetings);
    }

    public function getMeetingsByDateRange(Request $request, $boardId, $startDate, $endDate)
    {
        $board = Board::query()->findOrFail($boardId);
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $meetings = $board->meetings()->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('datetime', [$startDate, $endDate])
                ->orWhere(function ($query) use ($endDate) {
                    $query->whereNotNull('repeat')
                        ->where('datetime', '<=', $endDate);
                });
        })->get();

        $occurrences = collect();

        foreach ($meetings as $meeting) {
            if ($meeting->repeat !== 'none') {
                $interval = $this->getRepeatInterval($meeting->repeat);
                if ($interval) {
                    $occurrenceDate = Carbon::parse($meeting->datetime);

                    while ($occurrenceDate->lt($startDate) || $this->isWeekend($occurrenceDate)) {
                        $occurrenceDate->add($interval);
                    }

                    while ($occurrenceDate->lte($endDate)) {
                        if (!$this->isWeekend($occurrenceDate)) {
                            $occurrence = $meeting->replicate();
                            $occurrence->id = $meeting->id;
                            $occurrence->datetime = $occurrenceDate->toDateTimeString();
                            $occurrences->push($occurrence);
                        }
                        $occurrenceDate->add($interval);
                    }
                }
            } else {
                $meetingDate = Carbon::parse($meeting->datetime);

                if ($meetingDate->between($startDate, $endDate)) {
                    $occurrences->push($meeting);
                }
            }
        }


        return response()->json($occurrences);
    }

    /**
     * Get the repeat interval based on the recurrence type.
     */
    private function getRepeatInterval($repeat)
    {
        return match ($repeat) {
            'daily'   => CarbonInterval::day(),
            'weekly'  => CarbonInterval::week(),
            'monthly' => CarbonInterval::month(),
            default   => null,
        };
    }

    /**
     * Check if a given date falls on a weekend (Saturday or Sunday).
     */
    private function isWeekend(Carbon $date): bool
    {
        return $date->isSaturday() || $date->isSunday();
    }

    public function notifyLate(Request $request, $meetingId)
    {
        $request->validate([
            'datetime' => 'required|date',
        ]);

        $datetime = $request->datetime;
        $meeting = Meeting::query()->findOrFail($meetingId);
        $board = $meeting->board;
        $users = $board->users;
        $requestUser = $request->user();

        foreach ($users as $user) {
            Mail::raw("User {$requestUser->name} will be late for the meeting '{$meeting->name}' scheduled at {$datetime}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Meeting Late Notification');
            });
        }

        return response()->json(['message' => 'Notification sent to all board users.']);
    }

    public function notifyAbsent(Request $request, $meetingId)
    {
        $request->validate([
            'datetime' => 'required|date',
        ]);

        $datetime = $request->datetime;
        $meeting = Meeting::query()->findOrFail($meetingId);
        $board = $meeting->board;
        $users = $board->users;
        $requestUser = $request->user();

        foreach ($users as $user) {
            Mail::raw("User {$requestUser->name} will not be able to attend the meeting '{$meeting->name}' scheduled at {$datetime}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Meeting Absent Notification');
            });
        }

        return response()->json(['message' => 'Notification sent to all board users.']);
    }

}
