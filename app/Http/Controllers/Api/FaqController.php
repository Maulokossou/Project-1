<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqCategoryResource;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $categories = FaqCategory::with(['faqs' => function($query) {
            $query->active()->orderBy('order');
        }])->get();

        return FaqCategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'faq_category_id' => 'required|exists:faq_categories,id',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $faq = Faq::create($validated);

        return new FaqResource($faq);
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'faq_category_id' => 'required|exists:faq_categories,id',
            'order' => 'integer',
            'is_active' => 'boolean'
        ]);

        $faq->update($validated);

        return new FaqResource($faq);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json(['message' => 'FAQ supprimée']);
    }

}