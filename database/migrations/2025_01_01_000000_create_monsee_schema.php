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
        // 1. Users Table (Induk Utama)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->boolean('is_premium')->default(false);
            $table->rememberToken();
            $table->integer('deleted_by')->nullable();
            $table->string('deleted_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Personal Access Tokens (Sanctum)
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        // 3. Configs (Independent)
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 4. Wallets (Depends on Users)
       Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category')->default('cash');
            $table->string('currency', 3)->default('USD'); 
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->string('icon');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Categories (Depends on Users)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade'); 
            $table->string('name');
            $table->string('icon');
            $table->string('color', 20)->default('#4CAF50');
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->timestamps(); 
            $table->softDeletes();
        });

        // 6. Transactions (Depends on Users, Wallets, Categories)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->string('currency', 3)->default('USD');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable(); 
            
            $table->dateTime('date');
            $table->timestamps();
        });

        // 7. Daily Rates (For Currency Conversions)
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('base_currency', 3)->default('USD');
            $table->json('rates');
            $table->timestamps();
            $table->unique(['date', 'base_currency']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('configs');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
    }
};