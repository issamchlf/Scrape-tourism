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
        Schema::create('scarpeds', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->enum('status', ['pending', 'succes', 'rejected'])->default('pending')->comment('pending, approved, rejected');
            $table->timestamp('last_scraped_at')->nullable()->comment('last time the attraction was scraped');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scarpeds');
    }
};
