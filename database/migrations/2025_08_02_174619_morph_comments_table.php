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
        Schema::table('comments', function (Blueprint $table) {
            // Remove the old post_id column
            $table->dropForeign(['post_id']);
            $table->dropColumn('post_id');

            $table->unsignedBigInteger('commentable_id')->after('id');
            $table->string('commentable_type')->after('commentable_id');
            $table->index(['commentable_id', 'commentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_id', 'commentable_type']);
            $table->dropColumn(['commentable_id', 'commentable_type']);
            $table->foreignIdFor(Post::class)->constrained()->onDelete('cascade');

        });
    }
};
