<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_hourly_rate_usd', 8, 2)->default(18);
            $table->decimal('default_workana_commission', 5, 2)->default(10);
            $table->decimal('fallback_cop_rate', 12, 4)->default(3245);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_settings');
    }
};
