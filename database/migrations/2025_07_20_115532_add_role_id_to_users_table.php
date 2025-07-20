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
        Schema::table('users', function (Blueprint $table) {
            // Adding the role_id column to the users table
            $table->unsignedBigInteger('role_id')->nullable(); // Add the role_id column (nullable for existing users)

            // Adding a foreign key constraint to link the role_id with the roles table
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('set null'); // If a role is deleted, set the role_id to null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint and the role_id column
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
