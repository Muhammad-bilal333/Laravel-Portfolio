<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Stu;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalFaculty = Faculty::count();
        $totalStudents = Stu::count();
        $totalQEC = User::where('role_id', 4)->count();

        // Recent Activity (latest 5 for each, then merge and sort)
        $recentFaculty = Faculty::with('user')->orderByDesc('updated_at')->take(5)->get()->map(function($item) {
            return [
                'type' => 'Faculty',
                'name' => $item->user->name ?? 'N/A',
                'timestamp' => $item->updated_at,
                'action' => $item->created_at->eq($item->updated_at) ? 'created' : 'updated',
            ];
        });
        $recentStudents = Stu::with('user')->orderByDesc('updated_at')->take(5)->get()->map(function($item) {
            return [
                'type' => 'Student',
                'name' => $item->user->name ?? 'N/A',
                'timestamp' => $item->updated_at,
                'action' => $item->created_at->eq($item->updated_at) ? 'created' : 'updated',
            ];
        });
        $recentQEC = User::where('role_id', 4)->orderByDesc('updated_at')->take(5)->get()->map(function($item) {
            return [
                'type' => 'QEC',
                'name' => $item->name,
                'timestamp' => $item->updated_at,
                'action' => $item->created_at->eq($item->updated_at) ? 'created' : 'updated',
            ];
        });

        // Merge and sort all activities by timestamp (desc)
        $recentActivity = $recentFaculty
            ->merge($recentStudents)
            ->merge($recentQEC)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();

        return view('admin.dashboard', compact('totalFaculty', 'totalStudents', 'totalQEC', 'recentActivity'));
    }
}
