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
        Schema::create('livraison_informations', function (Blueprint $table) {
            $table->id();
            $table->string('value_id'); 
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders'); // Clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraison_informations');
    }
};
