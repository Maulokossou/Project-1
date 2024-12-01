<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // Recherche de projets par nom
    public function searchProjects(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $projects = Project::where('name', 'like', '%' . $request->name . '%')
            ->with(['company'])
            ->paginate(20);

        return response()->json($projects);
    }

    // Recherche d'entreprises par nom
    public function searchCompanies(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        $companies = Company::where('name', 'like', '%' . $request->name . '%')
            ->withCount('projects')
            ->paginate(20);

        return response()->json($companies);
    }

    // Recherche de projets par montant
    public function searchProjectsByAmount(Request $request)
    {
        $request->validate([
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount'
        ]);

        $query = Project::query();

        if ($request->has('min_amount')) {
            $query->where('goal_amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('goal_amount', '<=', $request->max_amount);
        }

        $projects = $query->with(['company'])
            ->paginate(20);

        return response()->json($projects);
    }
}