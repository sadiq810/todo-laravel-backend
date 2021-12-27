<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Task;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * Load dashboard view.
     */
    public function index()
    {
        return view('dashboard',[
            'users'     => Customer::count(),
            'tasks' => Task::count(),
        ]);
    }//..... end of index() .....//
}
