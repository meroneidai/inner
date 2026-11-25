<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ReflectionAnswer;
use Illuminate\Http\Request;

class DevelopmentController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'themes' => $request->user()->developmentThemes()->with('actions')->get(),
        ]);
    }

    public function storeReflection(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'dream_analysis_id' => ['nullable', 'integer'],
        ]);

        $answer = ReflectionAnswer::query()->create([
            'user_id' => $request->user()->id,
            ...$data,
        ]);

        return response()->json($answer, 201);
    }
}
