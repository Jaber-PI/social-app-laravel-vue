<?php

use App\Models\Group;
use App\Models\User;
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
        Schema::create('group_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Group::class)->constrained();

            $table->foreignIdFor(User::class, 'added_by')->nullable()->constrained('users')->onDelete('set null'); // Foreign key for the user who added, with set null on delete

            $table->string('role',25)->default('member'); // Role within the group, default is 'member'
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status of the membership
            $table->timestamp('approved_at')->nullable(); // Timestamp for when the membership was approved

            $table->string('confirmation_token')->nullable(); // Token for confirming membership

            $table->timestamps();

            // Ensure each user can only belong to each group once
            $table->unique(['group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_users');
    }
};
