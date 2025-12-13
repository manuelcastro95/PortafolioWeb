<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'page_view', 'click', 'download', 'link_click'
            $table->string('event_name'); // 'cv_download', 'project_demo', 'linkedin_click', etc.
            $table->string('section')->nullable(); // 'navbar', 'projects', 'contact', etc.
            $table->string('target_url')->nullable(); // URL del link clickeado
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('device_type')->nullable(); // 'desktop', 'mobile', 'tablet'
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales (project_id, skill_id, etc.)
            $table->timestamps();
        });

        // Índices para mejorar las consultas
        Schema::table('analytics', function (Blueprint $table) {
            $table->index('event_type');
            $table->index('event_name');
            $table->index('section');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};

