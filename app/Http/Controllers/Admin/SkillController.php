<?php

namespace App\Http\Controllers\Admin;

use App\Support\StorageDisk;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('order')->get()->map(function ($skill) {
            $skillData = $skill->toArray();
            $skillData['icon_url'] = $skill->icon_url;
            return $skillData;
        });

        return Inertia::render('Admin/Skills/Index', [
            'skills' => $skills,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|max:1024',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $diskName = StorageDisk::name();
            $path = $request->file('icon')->store('skills', $diskName);
            if ($diskName === 's3') {
                Storage::disk($diskName)->setVisibility($path, 'public');
            }
            $validated['icon'] = $path;
        }

        Skill::create($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Habilidad creada exitosamente.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|max:1024',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('icon')) {
            $diskName = StorageDisk::name();
            if ($skill->icon) {
                Storage::disk($diskName)->delete($skill->icon);
            }
            $path = $request->file('icon')->store('skills', $diskName);
            if ($diskName === 's3') {
                Storage::disk($diskName)->setVisibility($path, 'public');
            }
            $validated['icon'] = $path;
        }

        $skill->update($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Habilidad actualizada exitosamente.');
    }

    public function destroy(Skill $skill)
    {
        if ($skill->icon) {
            $diskName = StorageDisk::name();
            Storage::disk($diskName)->delete($skill->icon);
        }
        
        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('success', 'Habilidad eliminada exitosamente.');
    }
}

