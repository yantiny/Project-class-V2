<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //1.Tracking progress student
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['complete', 'on_progress'])->default('on_progress');
            $table->float('percentage')->default(0.0);
            $table->timestamps();
        });

        //2.Quiz Result
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->integer('score');
            $table->timestamp('completed_at')->useCurrent();

        });

        //3. Certificate
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_url');
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('quiz_results');
        Schema::dropIfExists('progress');
    }
};
