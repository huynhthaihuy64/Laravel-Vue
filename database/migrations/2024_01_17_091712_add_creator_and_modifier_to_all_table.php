<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatorAndModifierToAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Creator ID');
            $table->foreignId('modifier_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Modifier ID');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Creator ID');
            $table->foreignId('modifier_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Modifier ID');
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Creator ID');
            $table->foreignId('modifier_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('Modifier ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['modifier_id']);
        });
        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['modifier_id']);
        });
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropForeign(['modifier_id']);
        });
    }
}
