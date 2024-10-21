<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CampaignLaunchedMail;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        //$this->sendCampaignLaunchEmails($campaign);


        return response()->json(['message' => 'Campaign created successfully', 'campaign' => $campaign], 201);
    }
    private function sendCampaignLaunchEmails(Campaign $campaign)
    {
        $users = User::where('role', 'collaborator')->orWhere('role', 'client')->get();

        foreach ($users as $user) {
            $token = Str::random(60);
            $user->update(['vote_token' => $token]);

            $voteUrl = url("/vote/{$campaign->id}?token={$token}");

            Mail::to($user->email)->send(new CampaignLaunchedMail($campaign, $voteUrl));
        }
    }

/*     public function vote(Request $request, Campaign $campaign)
    {
        $request->validate([
            'votes' => 'required|array',
            'votes.*.project_id' => 'required|exists:projects,id',
            'votes.*.amount' => 'required|numeric|min:0',
            'token' => 'required|string',
        ]);

        $user = User::where('vote_token', $request->token)->firstOrFail();

        DB::transaction(function () use ($request, $campaign, $user) {
            foreach ($request->votes as $vote) {
                Vote::create([
                    'campaign_id' => $campaign->id,
                    'project_id' => $vote['project_id'],
                    'voter_email' => $user->email,
                    'amount' => $vote['amount'],
                ]);
            }

            $user->update(['vote_token' => null]);
        });

        return response()->json(['message' => 'Votes recorded successfully']);
    } */

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