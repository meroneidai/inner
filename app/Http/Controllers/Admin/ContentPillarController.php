<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content\Pillar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentPillarController extends Controller
{
    public function index(): View
    {
        return view('admin.content.pillars.index', [
            'pillars' => Pillar::query()->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function edit(Pillar $pillar): View
    {
        return view('admin.content.pillars.edit', compact('pillar'));
    }

    public function update(Request $request, Pillar $pillar): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'intro' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['status'] === 'published' && ! $pillar->published_at) {
            $data['published_at'] = now();
        }

        $pillar->update($data);

        return back()->with('status', __('تم حفظ الركيزة.'));
    }
}
