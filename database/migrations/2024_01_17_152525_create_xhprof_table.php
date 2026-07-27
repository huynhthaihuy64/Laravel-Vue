<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateXHProfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id('idcount');
            $table->string('id', 64);
            $table->string('url', 255)->nullable();
            $table->string('c_url', 255)->nullable();
            $table->timestamp('timestamp')->useCurrent()->useCurrentOnUpdate();
            $table->string('server name', 64)->nullable();
            $table->binary('perfdata')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->binary('cookie')->nullable();
            $table->binary('post')->nullable();
            $table->binary('get')->nullable();
            $table->integer('pmu')->nullable();
            $table->integer('wt')->nullable();
            $table->integer('cpu')->nullable();
            $table->string('server_id', 64)->nullable();
            $table->string('aggregateCalls_include', 255)->nullable();

            $table->index('url');
            $table->index('c_url');
            $table->index('cpu');
            $table->index('wt');
            $table->index('pmu');
            $table->index('timestamp');
            $table->index(['server name', 'timestamp']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE details MODIFY COLUMN `perfdata` LONGBLOB');
            DB::statement('ALTER TABLE details MODIFY COLUMN `cookie` LONGBLOB');
            DB::statement('ALTER TABLE details MODIFY COLUMN `post` LONGBLOB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('details');
    }
};
