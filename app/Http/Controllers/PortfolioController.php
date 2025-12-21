<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\User;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function index()
    {
        $user = User::first();
        
        // Agregar las URLs completas al usuario
        if ($user) {
            $userData = $user->toArray();
            $userData['profile_image_url'] = $user->profile_image_url;
            $userData['cv_file_url'] = $user->cv_file_url;
            $user = $userData;
        }
        
        $allProjects = Project::with(['images', 'skills'])
            ->where('is_active', true)
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
        
        $projects = $allProjects->where('type', 'proyecto')->values();
        $collaborations = $allProjects->where('type', 'colaboracion')->values();
        
        $skills = Skill::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($skill) {
                $skillData = $skill->toArray();
                $skillData['icon_url'] = $skill->icon_url;
                return $skillData;
            });
        $experiences = Experience::where('is_active', true)
            ->orderBy('order')
            ->get();

        return Inertia::render('Portfolio/Index', [
            'user' => $user,
            'projects' => $projects,
            'collaborations' => $collaborations,
            'skills' => $skills,
            'experiences' => $experiences,
        ]);
    }
}

