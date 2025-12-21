<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['images', 'skills'])
            ->orderBy('order')
            ->get()
            ->map(function ($project) {
                $projectData = $project->toArray();
                $projectData['images'] = $project->images->map(function ($image) {
                    $imageData = $image->toArray();
                    $imageData['image_url'] = $image->image_url;
                    return $imageData;
                })->toArray();
                return $projectData;
            });

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $skills = Skill::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Projects/Create', [
            'skills' => $skills,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:proyecto,colaboracion',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'github' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'link' => $validated['link'] ?? null,
            'github' => $validated['github'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (isset($validated['skills'])) {
            $project->skills()->sync($validated['skills']);
        }

        if ($request->hasFile('images')) {
            $diskName = env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET') ? 's3' : config('filesystems.default');
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('projects', $diskName);
                if ($diskName === 's3') {
                    Storage::disk($diskName)->setVisibility($path, 'public');
                }
                
                $project->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Proyecto creado exitosamente.');
    }

    public function edit(Project $project)
    {
        $project->load(['images', 'skills']);
        $skills = Skill::where('is_active', true)->orderBy('name')->get();

        $projectData = $project->toArray();
        $projectData['images'] = $project->images->map(function ($image) {
            $imageData = $image->toArray();
            $imageData['image_url'] = $image->image_url;
            return $imageData;
        })->toArray();

        return Inertia::render('Admin/Projects/Edit', [
            'project' => $projectData,
            'skills' => $skills,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:proyecto,colaboracion',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'github' => 'nullable|url',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:project_images,id',
        ]);

        $project->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'link' => $validated['link'] ?? null,
            'github' => $validated['github'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (isset($validated['skills'])) {
            $project->skills()->sync($validated['skills']);
        }

        if (isset($validated['delete_images'])) {
            $diskName = env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET') ? 's3' : config('filesystems.default');
            foreach ($validated['delete_images'] as $imageId) {
                $image = $project->images()->find($imageId);
                if ($image) {
                    Storage::disk($diskName)->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            $diskName = env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET') ? 's3' : config('filesystems.default');
            $maxOrder = $project->images()->max('order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('projects', $diskName);
                if ($diskName === 's3') {
                    Storage::disk($diskName)->setVisibility($path, 'public');
                }
                
                $project->images()->create([
                    'image_path' => $path,
                    'order' => $maxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Proyecto actualizado exitosamente.');
    }

    public function destroy(Project $project)
    {
        $diskName = env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET') ? 's3' : config('filesystems.default');
        foreach ($project->images as $image) {
            Storage::disk($diskName)->delete($image->image_path);
        }
        
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Proyecto eliminado exitosamente.');
    }
}

