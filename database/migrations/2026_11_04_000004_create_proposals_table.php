<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();

            // Datos del proyecto (capturados / pegados)
            $table->string('workana_url')->nullable();
            $table->string('project_title');
            $table->longText('project_description')->nullable();
            $table->json('skills')->nullable();
            $table->date('published_at')->nullable();
            $table->unsignedInteger('proposals_count')->nullable();

            // Datos del cliente
            $table->string('client_name')->nullable();
            $table->unsignedInteger('client_published_projects')->nullable();
            $table->unsignedInteger('client_paid_projects')->nullable();
            $table->string('client_member_since')->nullable();

            // Economía
            $table->decimal('hourly_rate_usd', 8, 2)->default(18);
            $table->decimal('workana_commission', 5, 2)->default(10);
            $table->decimal('usd_to_cop_rate', 12, 4)->nullable();

            // Estructura de la propuesta
            $table->json('phases')->nullable();        // [{name, items:[{description, hours}]}]
            $table->json('workana_tasks')->nullable();  // [{key, label, status, note}]
            $table->json('text_blocks')->nullable();     // {key: {title, content}}

            // Entregables de texto
            $table->longText('details_text')->nullable();  // Detalles de la propuesta
            $table->string('delivery_time')->nullable();    // ¿Cuánto tiempo necesitas?

            // Seguimiento
            $table->enum('status', ['borrador', 'enviada', 'en_conversacion', 'ganada', 'perdida'])
                ->default('borrador');
            $table->date('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
