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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('facebook_account_id')->nullable()->constrained()->nullOnDelete();

            $table->string('platform')->nullable(); // e.g., 'facebook', 'twitter', etc.

            // Facebook page id
            $table->string('channel_id');

            $table->string('channel_name');

            $table->text('channel_description')
                ->nullable();

            $table->longText('access_token');

            $table->timestamps();

            $table->unique([
                'facebook_account_id',
                'channel_id'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
