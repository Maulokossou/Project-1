<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $campaign = auth()->user()->campaigns()->create([
            'total_amount' => $request->total_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Campaign created successfully', 'campaign' => $campaign], 201);
    }

    public function vote(Request $request, Campaign $campaign)
    {
        $request->validate([
            'votes' => 'required|array',
            'votes.*.project_id' => 'required|exists:projects,id',
            'votes.*.amount' => 'required|numeric|min:0',
            'voter_email' => 'required|email',
        ]);

        DB::transaction(function () use ($request, $campaign) {
            foreach ($request->votes as $vote) {
                Vote::create([
                    'campaign_id' => $campaign->id,
                    'project_id' => $vote['project_id'],
                    'voter_email' => $request->voter_email,
                    'amount' => $vote['amount'],
                ]);
            }
        });

        return response()->json(['message' => 'Votes recorded successfully']);
    }

    public function results(Campaign $campaign)
    {
        $results = $campaign->votes()
            ->select('project_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('project_id')
            ->with('project')
            ->get();

        return response()->json(['results' => $results]);
    }
}