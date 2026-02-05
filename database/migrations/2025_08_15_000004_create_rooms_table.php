<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name')->default('Pièce principale');
            $table->decimal('width', 8, 2);
            $table->decimal('length', 8, 2);
            $table->decimal('height', 8, 2)->default(2.50);
            $table->string('floor_material')->default('wood');
            $table->string('floor_color')->default('#C4A882');
            $table->string('wall_material')->default('paint');
            $table->string('wall_color')->default('#FFFFFF');
            $table->json('lighting_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
