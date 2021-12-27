<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class CheckOverDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tasks = Task::whereDate('due_date', '<', now())
            ->whereTime('due_date', '<', now())
            ->where('is_complete', 0)
            ->where('is_notify', 0)
            ->limit(200)
            ->get();

        info('Tasks Found: '. $tasks->count());

        foreach ($tasks as $task) {
            $task->update(['is_notify'=> 1]);
            event(new \App\Events\Notify($task));
        }

        return 0;
    }
}
