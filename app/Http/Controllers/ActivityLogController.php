<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer', 'subject')->latest()->paginate(20);
        return view('activity-logs.index', compact('activities'));
    }
}
