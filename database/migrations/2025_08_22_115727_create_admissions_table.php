<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained()->onDelete('cascade');

            $table->year('year'); // سنة القبول (مثلاً 2024)
            $table->decimal('min_average', 5, 2)->nullable(); // أقل معدل
            $table->decimal('min_total', 8, 2)->nullable();   // أقل مجموع
            $table->integer('preference_score')->nullable(); // درجة التفضيل
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
