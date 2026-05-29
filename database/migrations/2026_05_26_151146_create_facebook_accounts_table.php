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
        Schema::create('facebook_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('facebook_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('avatar')->nullable();
            $table->text('access_token');
            $table->timestamp('expires_in')->nullable();
            $table->string('page_id')->nullable();
            $table->string('page_name')->nullable();
            $table->longText('page_access_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_accounts');
    }
};
