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
        Schema::create('money_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_user_id');
            $table->unsignedBigInteger('receiver_user_id');
            $table->double('amount')->default(0);

            $table->foreign('sender_user_id')->references('id')->on('users');
            $table->foreign('receiver_user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_transfers');
    }
};
