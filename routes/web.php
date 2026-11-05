<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');

// Analytics API (public, no requiere autenticación)
Route::post('/api/analytics/track', [AnalyticsController::class, 'track'])->name('api.analytics.track');

// Auth routes
Route::get('/dashboard', function () {
    $user = \App\Models\User::first();
    $projectsCount = \App\Models\Project::count();
    $activeProjectsCount = \App\Models\Project::where('is_active', true)->count();
    $skillsCount = \App\Models\Skill::count();
    $activeSkillsCount = \App\Models\Skill::where('is_active', true)->count();
    $experiencesCount = \App\Models\Experience::count();
    $activeExperiencesCount = \App\Models\Experience::where('is_active', true)->count();
    
    return Inertia::render('Dashboard', [
        'stats' => [
            'projects' => [
                'total' => $projectsCount,
                'active' => $activeProjectsCount,
            ],
            'skills' => [
                'total' => $skillsCount,
                'active' => $activeSkillsCount,
            ],
            'experiences' => [
                'total' => $experiencesCount,
                'active' => $activeExperiencesCount,
            ],
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Profile
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['patch', 'post'], '/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    
    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::match(['put', 'patch', 'post'], '/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    
    // Skills
    Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::match(['patch', 'post'], '/skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');
    
    // Experiences
    Route::get('/experiences', [ExperienceController::class, 'index'])->name('experiences.index');
    Route::post('/experiences', [ExperienceController::class, 'store'])->name('experiences.store');
    Route::match(['patch', 'post'], '/experiences/{experience}', [ExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('/experiences/{experience}', [ExperienceController::class, 'destroy'])->name('experiences.destroy');
    
    // Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

    // Proposals (Propuestas de Workana)
    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/rate', [ProposalController::class, 'rate'])->name('proposals.rate');
    Route::post('/proposals/scrape', [ProposalController::class, 'scrape'])->name('proposals.scrape');
    Route::post('/proposals/settings', [ProposalController::class, 'updateSettings'])->name('proposals.settings');
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::patch('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->name('proposals.status');
    Route::match(['put', 'patch'], '/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'pdf'])->name('proposals.pdf');
});

require __DIR__.'/auth.php';
