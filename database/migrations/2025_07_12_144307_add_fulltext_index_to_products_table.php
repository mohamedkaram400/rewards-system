<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fulltext index for name and description
        DB::statement('ALTER TABLE products ADD FULLTEXT fulltext_name_description (name, description)');
        DB::statement('ALTER TABLE categories ADD FULLTEXT fulltext_name (name)');

        // Regular indexes for filtering fields
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('is_offer_pool');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_and_categories', function (Blueprint $table) {
            //
        });
    }
};
