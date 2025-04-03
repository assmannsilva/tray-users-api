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
            $table->timestamps();
            $table->binary("google_token");
            $table->binary('name')->nullable();
            $table->binary("birthday")->nullable();
            $table->binary("cpf")->nullable();
            $table->string("cpf_index")->nullable()->unique();
            $table->string("first_name_index")->nullable()->index();
            $table->json("surname_tokens")->nullable();
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
