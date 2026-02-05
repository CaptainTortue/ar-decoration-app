<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('furniture_object_id')->constrained('furniture_objects')->cascadeOnDelete();
            $table->decimal('position_x', 10, 4)->default(0);
            $table->decimal('position_y', 10, 4)->default(0);
            $table->decimal('position_z', 10, 4)->default(0);
            $table->decimal('rotation_x', 10, 4)->default(0);
            $table->decimal('rotation_y', 10, 4)->default(0);
            $table->decimal('rotation_z', 10, 4)->default(0);
            $table->decimal('scale_x', 5, 3)->default(1.000);
            $table->decimal('scale_y', 5, 3)->default(1.000);
            $table->decimal('scale_z', 5, 3)->default(1.000);
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_objects');
    }
};
