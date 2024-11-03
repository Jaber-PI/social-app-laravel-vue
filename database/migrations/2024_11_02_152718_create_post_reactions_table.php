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
        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Post::class)->constrained()->onDelete('cascade'); // Foreign key with cascade on delete
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade'); // Foreign key with cascade on delete
            $table->enum('reaction_type',['like','dislike','sad','laugh']); // Stores the type of reaction, e.g., 'like', 'dislike', etc.
            $table->timestamps();

            //Ensure each user can only have one reaction per post
            $table->unique(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
    }
};
