<?php

use App\Models\Post;
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

        Schema::table('post_reactions', function (Blueprint $table) {
            $table->dropForeign(['post_id']); // Drop the foreign key constraint for post_id
            $table->dropUnique(['post_id', 'user_id']);
            $table->renameColumn('post_id', 'reactable_id'); // Rename post_id to reactable_id
            $table->string('reactable_type')->after('reactable_id')->default('App\\Models\\Post');
        });

        Schema::rename('post_reactions', 'reactions');

        Schema::table('reactions', function (Blueprint $table) {
            $table->unique(['reactable_id', 'reactable_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique(['reactable_id', 'reactable_type', 'user_id']); // Drop the unique constraint for polymorphic relation
            $table->dropColumn('reactable_type');
            $table->renameColumn('reactable_id', 'post_id');
        });

        Schema::rename('reactions', 'post_reactions');

        Schema::table('post_reactions', function (Blueprint $table) {
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->unique(['post_id', 'user_id']);
        });
    }
};
