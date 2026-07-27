<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->comment('User ID');
            $table->string('modified_file_name')->comment('Modified Name file');
            $table->string('file_name')->comment('Name file');
            $table->string('file_path')->comment('Path file');
            $table->tinyInteger('status')->default(1)
                ->comment('1:unread, 2:failed, 3:succeed');
            $table->tinyInteger('type')
                ->comment('1:proportion_file, 2:result_file, 3:performance_approval_file');
            $table->string('file_type')->comment('File type');
            $table->integer('file_size')->comment('File size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
