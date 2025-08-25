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

            $table->string('college_type'); // نوع الكلية
            $table->integer('study_duration'); // سنوات الدراسة
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete(); // القسم الافتراضي
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};

