<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->string('sort_mode')->default('chronological');
            $table->string('share_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timeline_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->date('event_date');
            $table->text('description')->nullable();
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->index(['timeline_id', 'event_date']);
            $table->index(['timeline_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('timelines');
    }
};
