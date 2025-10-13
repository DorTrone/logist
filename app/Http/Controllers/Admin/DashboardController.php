<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Task;
use App\Models\Visitor;

class DashboardController extends Controller
{
    public function index()
    {
        $visitors = [
            [
                'name' => trans('app.web'),
                'count' => Visitor::where('visitors.updated_at', '>', now()->subMinute())
                    ->where('api', 0)
                    ->where('robot', 0)
                    ->count(),
            ],
            [
                'name' => trans('app.api'),
                'count' => Visitor::where('visitors.updated_at', '>', now()->subMinute())
                    ->where('api', 1)
                    ->where('robot', 0)
                    ->count(),
            ],
        ];

        $tasks = Task::whereHas('users', function ($query) {
            $query->where('id', auth()->id());
        })
            ->orderBy('id', 'desc')
            ->get();

        $objs = [];

        foreach ($tasks as $task) {
            $objs[] = [
                'name' => $task->getName(),
                'count' => intval(Package::filterQuery(
                    'web',
                    [],
                    [],
                    $task->queries['locations'],
                    $task->queries['transportTypes'],
                    $task->queries['packagePayments'],
                    $task->queries['packageTypes'],
                    $task->queries['packageStatuses'],
                    $task->queries['paymentStatuses'],
                )->count()),
                'link' => route('admin.packages.index', $task->queries),
            ];
        }

        $objs = collect($objs)->sortByDesc('count');

        return view('admin.dashboard.index')
            ->with([
                'visitors' => $visitors,
                'objs' => $objs,
            ]);
    }
}
