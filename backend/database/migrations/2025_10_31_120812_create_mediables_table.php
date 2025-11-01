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
        Schema::create('mediables', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->morphs('mediable');
            $table->string('tag');
            $table->primary(['media_id', 'mediable_id', 'mediable_type', 'tag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediables');
    }
};