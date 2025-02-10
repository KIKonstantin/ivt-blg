<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Blog title
            $table->text('description');
            $table->longText('content'); // Blog content
            $table->text('slug')->unique(); // Unique slug for SEO
            $table->string('blog_image')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('gallery_id')->nullable()->constrained('galleries')->nullOnDelete();
            $table->timestamps(); // Created at and updated at timestamps
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
