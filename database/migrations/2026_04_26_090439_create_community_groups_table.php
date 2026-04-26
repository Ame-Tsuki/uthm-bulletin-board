<?php
// database/migrations/2024_01_01_000001_create_community_groups_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('community_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('category');
            $table->string('cover_image')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('privacy', ['public', 'private', 'by_approval'])->default('public');
            $table->integer('max_members')->nullable();
            $table->boolean('allow_posts')->default(true);
            $table->boolean('allow_events')->default(true);
            $table->boolean('require_approval')->default(false);
            $table->json('tags')->nullable();
            $table->json('settings')->nullable();
            $table->integer('member_count')->default(0);
            $table->timestamps();
        });

        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });

        Schema::create('group_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->json('media')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        Schema::create('group_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('group_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('group_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('group_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['post_id', 'user_id']);
        });

        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_join_requests');
        Schema::dropIfExists('group_post_likes');
        Schema::dropIfExists('group_post_comments');
        Schema::dropIfExists('group_posts');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('community_groups');
    }
}