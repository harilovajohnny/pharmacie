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
        Schema::create('user_info_medicals', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('current_medical_condition')->nullable;
            $table->string('drug_allergie')->nullable;
            $table->longText('photo_prescription');
            $table->unsignedBigInteger('order_id'); // La colonne pour la clé étrangère
            $table->foreign('order_id')->references('id')->on('orders'); // Clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_info_medical');
    }
};
