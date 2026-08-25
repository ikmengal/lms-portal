<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('General');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['published_at', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
