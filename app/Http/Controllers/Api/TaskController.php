<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function list(Request $request)
    {
        return [
            "incomplete" => $this->recentIncompleteTasks($request),
            "complete" => $this->recentCompletedTasks($request)
        ];
    }

    public function complete(Request $request)
    {
        Task::where('id', $request->id)->update(['is_complete' => 1]);

        return ['status' => true, 'message' => 'Task marked completed successfully.'];
    }

    private function recentCompletedTasks(Request $request)
    {
        return Task::where(['customer_id' => $request->user()->id, 'is_complete' => 1])
            ->latest()->limit(20)->get();
    }

    private function recentIncompleteTasks(Request $request)
    {
        return Task::where(['customer_id' => $request->user()->id, 'is_complete' => 0])
            ->latest()->limit(20)->get()
            ->each(function ($task) {
                $due = Carbon::parse($task->due_date);

                if ($due->lessThan(now()))
                    $task->status = 0;
                else
                    $task->status = 1;
            });
    }

    public function create(Request $request)
    {
        $task = Task::create([
            'customer_id' => $request->user()->id,
            'title' => $request->title,
            'detail' => $request->detail,
            'due_date' => date("Y-m-d H:i:s", $request->due_date)
        ]);

        return ['status' => true, 'message' => 'Task created successfully.', 'data' => $task];
    }

    public function delete(Request $request)
    {
        $task = Task::where(['customer_id' => $request->user()->id, 'id' => $request->id])->first();

        if ($task)
            $task->delete();

        return ['status' => !!$task, 'message' => $task ? 'Task deleted successfully.': 'Task not found.'];
    }
}
