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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('species'); // dog, cat, etc.
            $table->string('breed')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable(); // male, female
            $table->text('description');
            $table->string('location');
            $table->decimal('price', 10, 2)->nullable(); // adoption fee
            $table->string('status')->default('available'); // available, adopted, pending
            $table->json('images')->nullable(); // array of image paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
