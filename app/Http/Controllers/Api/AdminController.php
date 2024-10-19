<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function allocateProjects(Request $request, Company $company)
    {
        $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $company->allocatedProjects()->sync($request->project_ids);

        return response()->json(['message' => 'Projects allocated successfully']);
    }
}