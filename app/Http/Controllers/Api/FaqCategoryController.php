<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::withCount('faqs')->get();
        return FaqCategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:faq_categories,name|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        $category = FaqCategory::create($validated);

        return new FaqCategoryResource($category);
    }

    public function show(FaqCategory $faqCategory)
    {
        return new FaqCategoryResource($faqCategory->loadCount('faqs'));
    }

    public function update(Request $request, FaqCategory $faqCategory)
    {
        $validated = $request->validate([
            'name' => 'required|unique:faq_categories,name,'.$faqCategory->id.'|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        $faqCategory->update($validated);

        return new FaqCategoryResource($faqCategory);
    }

    public function destroy(FaqCategory $faqCategory)
    {
        if ($faqCategory->faqs()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une catégorie contenant des FAQs'
            ], 400);
        }

        $faqCategory->delete();

        return response()->json(['message' => 'Catégorie supprimée']);
    }

}