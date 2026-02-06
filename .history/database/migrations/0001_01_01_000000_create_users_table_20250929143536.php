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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable for Google login
            $table->string('google_id')->nullable()->unique(); // For Google login
            
            // Columns for unlocked features via Rewarded Ads
            $table->boolean('has_unlimited_wallets')->default(false);
            $table->boolean('has_unlimited_categories')->default(false);
            $table->boolean('has_export_feature')->default(false);
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
