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
        Schema::create('scrapeds', function (Blueprint $table) {
            $table->id();
            $table->string('site_key')->nullable()->comment('unique key for the site');
            $table->string('url')->nullable();
            $table->enum('status', ['pending', 'success', 'error', 'rejected'])->default('pending')->comment('pending, success, error, rejected');
            $table->json('data_raw')->nullable()->comment('raw data in JSON format');
            $table->timestamp('last_scraped_at')->nullable()->comment('last time the attraction was scraped');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapeds');
    }
};
