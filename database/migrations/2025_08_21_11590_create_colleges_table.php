<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->tinyInteger('gender')->nullable();
            $table->foreignId('college_type_id')->constrained('college_types')->onDelete('cascade');
            $table->integer('study_duration');
            $table->timestamps();
            // Unique constraint
            $table->unique(
                ['name', 'university_id', 'branch_id', 'college_type_id', 'study_duration', 'gender'],
                'unique_college'
            );

            $table->index(
                ['university_id', 'college_type_id', 'branch_id', 'study_duration', 'gender'],
                'idx_colleges_filter'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
