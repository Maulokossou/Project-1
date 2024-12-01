<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with('client')
            ->when($request->client_id, function($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->when($request->type, function($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate(20);

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'ip_address' => 'nullable|ip'
        ]);

        $activity = Activity::create($validated);

        return response()->json($activity, 201);
    }
}