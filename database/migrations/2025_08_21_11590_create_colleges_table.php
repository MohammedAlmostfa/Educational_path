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
            $table->string('name');
            $table->enum('gender', [0, 1, 2])->nullable();
            $table->string('college_type');
            $table->integer('study_duration');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};

